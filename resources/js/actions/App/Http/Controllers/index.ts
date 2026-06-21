import Auth from './Auth'
import ReportController from './ReportController'
import PaymentController from './PaymentController'
import Preorder from './Preorder'
import MemberController from './MemberController'
import ContactController from './ContactController'
import ProkerDocumentController from './ProkerDocumentController'
import VotingController from './VotingController'
import DocumentationController from './DocumentationController'
import Admin from './Admin'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    ReportController: Object.assign(ReportController, ReportController),
    PaymentController: Object.assign(PaymentController, PaymentController),
    Preorder: Object.assign(Preorder, Preorder),
    MemberController: Object.assign(MemberController, MemberController),
    ContactController: Object.assign(ContactController, ContactController),
    ProkerDocumentController: Object.assign(ProkerDocumentController, ProkerDocumentController),
    VotingController: Object.assign(VotingController, VotingController),
    DocumentationController: Object.assign(DocumentationController, DocumentationController),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers