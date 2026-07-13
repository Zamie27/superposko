<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\Aspiration;
use App\Models\AspirationLike;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VotingController extends Controller
{
    /**
     * Display a listing of polls and aspirations.
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Fetch polls scoped by host posko
        $polls = Poll::where('host_id', $hostId)
            ->with(['options' => function ($query) {
                $query->withCount('votes');
            }, 'creator'])
            ->withCount('votes')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Poll $poll) use ($user) {
                // Check if this user has voted in this poll
                $userVote = PollVote::where('poll_id', $poll->id)
                    ->where('user_id', $user->id)
                    ->first();

                /** @var User $creator */
                $creator = $poll->creator;

                return [
                    'id' => $poll->id,
                    'title' => $poll->title,
                    'description' => $poll->description,
                    'image' => $poll->image ? \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($poll->image) : null,
                    'expires_at' => $poll->expires_at ? $poll->expires_at->toIso8601String() : null,
                    'is_expired' => $poll->isExpired(),
                    'created_by' => $creator->name,
                    'total_votes' => $poll->votes_count ?? 0,
                    'has_voted' => ! is_null($userVote),
                    'voted_option_id' => $userVote ? $userVote->poll_option_id : null,
                    'options' => $poll->options->map(function (PollOption $opt) {
                        return [
                            'id' => $opt->id,
                            'option_text' => $opt->option_text,
                            'votes_count' => $opt->votes_count ?? 0,
                        ];
                    })->all(),
                ];
            })->all();

        // Fetch aspirations scoped by host posko
        $aspirations = Aspiration::where('host_id', $hostId)
            ->with(['user'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Aspiration $asp) use ($user) {
                // Check if user has liked this suggestion
                $isLiked = AspirationLike::where('aspiration_id', $asp->id)
                    ->where('user_id', $user->id)
                    ->exists();

                /** @var User|null $aspUser */
                $aspUser = $asp->user;

                return [
                    'id' => $asp->id,
                    'title' => $asp->title,
                    'content' => $asp->content,
                    'status' => $asp->status,
                    'admin_response' => $asp->admin_response,
                    'is_anonymous' => $asp->is_anonymous,
                    'creator_name' => $asp->is_anonymous ? 'Anonim' : ($aspUser ? $aspUser->name : 'Mantan Anggota'),
                    'likes_count' => $asp->likes_count ?? 0,
                    'is_liked' => $isLiked,
                    'created_at' => $asp->created_at->diffForHumans(),
                ];
            })->all();

        return Inertia::render('voting/Index', [
            'polls' => $polls,
            'aspirations' => $aspirations,
            'canManage' => HostRoleHelper::isHostOrSekretaris($user),
        ]);
    }

    /**
     * Store a newly created poll.
     */
    public function storePoll(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat membuat voting.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'options' => ['required', 'array', 'min:2', 'max:10'],
            'options.*' => ['required', 'string', 'max:255'],
        ]);

        $hostId = $user->host_id ?? $user->id;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('polls', env('FILESYSTEM_DISK', 'public'));
        }

        $poll = Poll::create([
            'host_id' => $hostId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'created_by' => $user->id,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        foreach ($validated['options'] as $optionText) {
            PollOption::create([
                'poll_id' => $poll->id,
                'option_text' => $optionText,
            ]);
        }

        \App\Helpers\PushNotificationHelper::sendToHostGroup(
            $hostId,
            'Voting Baru Kelompok KKN',
            "Ada voting baru: '{$poll->title}' oleh {$user->name}. Silakan berikan suara Anda.",
            '/voting',
            $user->id
        );

        ActivityLogHelper::log(
            'voting',
            'create_poll',
            "User created a new poll: '{$poll->title}' with options."
        );

        return back()->with('success', 'Voting berhasil dibuat.');
    }

    /**
     * Cast a vote in a poll.
     */
    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($poll->host_id !== $hostId) {
            abort(403, 'Akses ditolak.');
        }

        if ($poll->isExpired()) {
            return back()->with('error', 'Voting ini sudah berakhir.');
        }

        // Check double vote
        $alreadyVoted = PollVote::where('poll_id', $poll->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyVoted) {
            return back()->with('error', 'Anda sudah memberikan suara pada voting ini.');
        }

        $validated = $request->validate([
            'poll_option_id' => ['required', 'exists:poll_options,id'],
        ]);

        // Validate option belongs to this poll
        /** @var PollOption|null $option */
        $option = PollOption::where('poll_id', $poll->id)
            ->where('id', $validated['poll_option_id'])
            ->first();
        if (! $option) {
            abort(400, 'Pilihan tidak valid.');
        }

        PollVote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'user_id' => $user->id,
        ]);

        ActivityLogHelper::log(
            'voting',
            'cast_vote',
            "User cast a vote on poll: '{$poll->title}' option '{$option->option_text}'."
        );

        return back()->with('success', 'Suara Anda berhasil dikirim.');
    }

    /**
     * Cancel/remove a cast vote.
     */
    public function cancelVote(Request $request, Poll $poll): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($poll->host_id !== $hostId) {
            abort(403, 'Akses ditolak.');
        }

        if ($poll->isExpired()) {
            return back()->with('error', 'Voting ini sudah berakhir.');
        }

        PollVote::where('poll_id', $poll->id)
            ->where('user_id', $user->id)
            ->delete();

        ActivityLogHelper::log(
            'voting',
            'cancel_vote',
            "User cancelled their vote on poll: '{$poll->title}'."
        );

        return back()->with('success', 'Pilihan Anda berhasil dibatalkan.');
    }

    /**
     * Delete a poll.
     */
    public function destroyPoll(Request $request, Poll $poll): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($poll->host_id !== $hostId || ! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat menghapus voting.');
        }

        if ($poll->image) {
            \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($poll->image);
        }

        $poll->delete();

        ActivityLogHelper::log(
            'voting',
            'delete_poll',
            "User deleted poll: '{$poll->title}'."
        );

        return back()->with('success', 'Voting berhasil dihapus.');
    }

    /**
     * Store a suggestion/aspiration.
     */
    public function storeAspiration(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_anonymous' => ['required', 'boolean'],
        ]);

        $aspiration = Aspiration::create([
            'host_id' => $hostId,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => $user->id,
            'is_anonymous' => $validated['is_anonymous'],
            'status' => 'pending',
        ]);

        $senderName = $aspiration->is_anonymous ? 'Seseorang (Anonim)' : $user->name;
        \App\Helpers\PushNotificationHelper::sendToHostGroup(
            $hostId,
            'Aspirasi Baru Kelompok KKN',
            "{$senderName} telah mengirim aspirasi baru: '{$aspiration->title}'.",
            '/voting',
            $user->id
        );

        ActivityLogHelper::log(
            'voting',
            'create_aspiration',
            "User submitted an aspiration: '{$aspiration->title}'"
        );

        return back()->with('success', 'Aspirasi berhasil dikirim.');
    }

    /**
     * Toggle upvote/like on an aspiration.
     */
    public function likeAspiration(Request $request, Aspiration $aspiration): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($aspiration->host_id !== $hostId) {
            abort(403, 'Akses ditolak.');
        }

        $like = AspirationLike::where('aspiration_id', $aspiration->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            AspirationLike::create([
                'aspiration_id' => $aspiration->id,
                'user_id' => $user->id,
            ]);
        }

        return back();
    }

    /**
     * Update aspiration response and status.
     */
    public function respondAspiration(Request $request, Aspiration $aspiration): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($aspiration->host_id !== $hostId || ! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat merespon aspirasi.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'review', 'resolved'])],
            'admin_response' => ['nullable', 'string'],
        ]);

        $aspiration->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'] ?? null,
        ]);

        ActivityLogHelper::log(
            'voting',
            'respond_aspiration',
            "User responded to aspiration '{$aspiration->title}' with status '{$validated['status']}'."
        );

        return back()->with('success', 'Aspirasi berhasil diperbarui.');
    }

    /**
     * Delete an aspiration.
     */
    public function destroyAspiration(Request $request, Aspiration $aspiration): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($aspiration->host_id !== $hostId || ! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat menghapus aspirasi.');
        }

        $aspiration->delete();

        ActivityLogHelper::log(
            'voting',
            'delete_aspiration',
            "User deleted aspiration: '{$aspiration->title}'."
        );

        return back()->with('success', 'Aspirasi berhasil dihapus.');
    }
}
