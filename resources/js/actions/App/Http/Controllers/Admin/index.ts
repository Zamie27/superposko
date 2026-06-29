import AdminController from './AdminController'
import AdminUserController from './AdminUserController'
import AdminPriceController from './AdminPriceController'
import AdminSubscriptionController from './AdminSubscriptionController'
import AdminTrialController from './AdminTrialController'
import AdminPreorderController from './AdminPreorderController'
import AdminSubscriptionRequestController from './AdminSubscriptionRequestController'
import AdminNotificationController from './AdminNotificationController'
import AdminSettingController from './AdminSettingController'
import AdminDocumentationConfigController from './AdminDocumentationConfigController'
import AdminActivityLogController from './AdminActivityLogController'
import AdminReportController from './AdminReportController'
import AdminBugReportController from './AdminBugReportController'

const Admin = {
    AdminController: Object.assign(AdminController, AdminController),
    AdminUserController: Object.assign(AdminUserController, AdminUserController),
    AdminPriceController: Object.assign(AdminPriceController, AdminPriceController),
    AdminSubscriptionController: Object.assign(AdminSubscriptionController, AdminSubscriptionController),
    AdminTrialController: Object.assign(AdminTrialController, AdminTrialController),
    AdminPreorderController: Object.assign(AdminPreorderController, AdminPreorderController),
    AdminSubscriptionRequestController: Object.assign(AdminSubscriptionRequestController, AdminSubscriptionRequestController),
    AdminNotificationController: Object.assign(AdminNotificationController, AdminNotificationController),
    AdminSettingController: Object.assign(AdminSettingController, AdminSettingController),
    AdminDocumentationConfigController: Object.assign(AdminDocumentationConfigController, AdminDocumentationConfigController),
    AdminActivityLogController: Object.assign(AdminActivityLogController, AdminActivityLogController),
    AdminReportController: Object.assign(AdminReportController, AdminReportController),
    AdminBugReportController: Object.assign(AdminBugReportController, AdminBugReportController),
}

export default Admin