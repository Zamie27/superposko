import ProfileController from './ProfileController'
import EmailChangeController from './EmailChangeController'
import SecurityController from './SecurityController'
import ApiController from './ApiController'

const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
    EmailChangeController: Object.assign(EmailChangeController, EmailChangeController),
    SecurityController: Object.assign(SecurityController, SecurityController),
    ApiController: Object.assign(ApiController, ApiController),
}

export default Settings