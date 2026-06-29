import GoogleLoginController from './GoogleLoginController'
import EmailVerificationOtpController from './EmailVerificationOtpController'

const Auth = {
    GoogleLoginController: Object.assign(GoogleLoginController, GoogleLoginController),
    EmailVerificationOtpController: Object.assign(EmailVerificationOtpController, EmailVerificationOtpController),
}

export default Auth