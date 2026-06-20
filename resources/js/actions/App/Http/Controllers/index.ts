import Auth from './Auth'
import Preorder from './Preorder'
import PaymentController from './PaymentController'
import MemberController from './MemberController'
import DocumentationController from './DocumentationController'
import Admin from './Admin'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    Preorder: Object.assign(Preorder, Preorder),
    PaymentController: Object.assign(PaymentController, PaymentController),
    MemberController: Object.assign(MemberController, MemberController),
    DocumentationController: Object.assign(DocumentationController, DocumentationController),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers