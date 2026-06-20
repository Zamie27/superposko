import ProfileController from './ProfileController'
import SecurityController from './SecurityController'
import ApiController from './ApiController'

const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
    SecurityController: Object.assign(SecurityController, SecurityController),
    ApiController: Object.assign(ApiController, ApiController),
}

export default Settings