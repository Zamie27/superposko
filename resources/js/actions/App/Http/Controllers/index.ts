import Auth from './Auth'
import PaymentController from './PaymentController'
import MemberController from './MemberController'
import DocumentationController from './DocumentationController'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    PaymentController: Object.assign(PaymentController, PaymentController),
    MemberController: Object.assign(MemberController, MemberController),
    DocumentationController: Object.assign(DocumentationController, DocumentationController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers