import Auth from './Auth'
import ReportController from './ReportController'
import Preorder from './Preorder'
import PaymentController from './PaymentController'
import MemberController from './MemberController'
import DocumentationController from './DocumentationController'
import Admin from './Admin'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    ReportController: Object.assign(ReportController, ReportController),
    Preorder: Object.assign(Preorder, Preorder),
    PaymentController: Object.assign(PaymentController, PaymentController),
    MemberController: Object.assign(MemberController, MemberController),
    DocumentationController: Object.assign(DocumentationController, DocumentationController),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers