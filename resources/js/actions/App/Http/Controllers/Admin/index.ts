import AdminController from './AdminController'
import AdminUserController from './AdminUserController'
import AdminPriceController from './AdminPriceController'
import AdminSubscriptionController from './AdminSubscriptionController'
import AdminPreorderController from './AdminPreorderController'
import AdminSettingController from './AdminSettingController'
import AdminReportController from './AdminReportController'

const Admin = {
    AdminController: Object.assign(AdminController, AdminController),
    AdminUserController: Object.assign(AdminUserController, AdminUserController),
    AdminPriceController: Object.assign(AdminPriceController, AdminPriceController),
    AdminSubscriptionController: Object.assign(AdminSubscriptionController, AdminSubscriptionController),
    AdminPreorderController: Object.assign(AdminPreorderController, AdminPreorderController),
    AdminSettingController: Object.assign(AdminSettingController, AdminSettingController),
    AdminReportController: Object.assign(AdminReportController, AdminReportController),
}

export default Admin