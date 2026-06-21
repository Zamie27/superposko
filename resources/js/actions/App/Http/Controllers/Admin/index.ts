import AdminController from './AdminController'
import AdminUserController from './AdminUserController'
import AdminPriceController from './AdminPriceController'
import AdminSubscriptionController from './AdminSubscriptionController'
import AdminPreorderController from './AdminPreorderController'
import AdminSettingController from './AdminSettingController'
import AdminDocumentationConfigController from './AdminDocumentationConfigController'
import AdminActivityLogController from './AdminActivityLogController'
import AdminReportController from './AdminReportController'

const Admin = {
    AdminController: Object.assign(AdminController, AdminController),
    AdminUserController: Object.assign(AdminUserController, AdminUserController),
    AdminPriceController: Object.assign(AdminPriceController, AdminPriceController),
    AdminSubscriptionController: Object.assign(AdminSubscriptionController, AdminSubscriptionController),
    AdminPreorderController: Object.assign(AdminPreorderController, AdminPreorderController),
    AdminSettingController: Object.assign(AdminSettingController, AdminSettingController),
    AdminDocumentationConfigController: Object.assign(AdminDocumentationConfigController, AdminDocumentationConfigController),
    AdminActivityLogController: Object.assign(AdminActivityLogController, AdminActivityLogController),
    AdminReportController: Object.assign(AdminReportController, AdminReportController),
}

export default Admin