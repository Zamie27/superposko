import Auth from './Auth'
import ReportController from './ReportController'
import BugReportController from './BugReportController'
import PaymentController from './PaymentController'
import TripayController from './TripayController'
import Preorder from './Preorder'
import Payment from './Payment'
import DashboardController from './DashboardController'
import FinanceController from './FinanceController'
import LogbookController from './LogbookController'
import InventoryController from './InventoryController'
import LogisticController from './LogisticController'
import PersonalBelongingController from './PersonalBelongingController'
import ScheduleController from './ScheduleController'
import MemberController from './MemberController'
import MemberActivityLogController from './MemberActivityLogController'
import ContactController from './ContactController'
import ProkerDocumentController from './ProkerDocumentController'
import VotingController from './VotingController'
import DocumentationController from './DocumentationController'
import PushSubscriptionController from './PushSubscriptionController'
import Admin from './Admin'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    ReportController: Object.assign(ReportController, ReportController),
    BugReportController: Object.assign(BugReportController, BugReportController),
    PaymentController: Object.assign(PaymentController, PaymentController),
    TripayController: Object.assign(TripayController, TripayController),
    Preorder: Object.assign(Preorder, Preorder),
    Payment: Object.assign(Payment, Payment),
    DashboardController: Object.assign(DashboardController, DashboardController),
    FinanceController: Object.assign(FinanceController, FinanceController),
    LogbookController: Object.assign(LogbookController, LogbookController),
    InventoryController: Object.assign(InventoryController, InventoryController),
    LogisticController: Object.assign(LogisticController, LogisticController),
    PersonalBelongingController: Object.assign(PersonalBelongingController, PersonalBelongingController),
    ScheduleController: Object.assign(ScheduleController, ScheduleController),
    MemberController: Object.assign(MemberController, MemberController),
    MemberActivityLogController: Object.assign(MemberActivityLogController, MemberActivityLogController),
    ContactController: Object.assign(ContactController, ContactController),
    ProkerDocumentController: Object.assign(ProkerDocumentController, ProkerDocumentController),
    VotingController: Object.assign(VotingController, VotingController),
    DocumentationController: Object.assign(DocumentationController, DocumentationController),
    PushSubscriptionController: Object.assign(PushSubscriptionController, PushSubscriptionController),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers