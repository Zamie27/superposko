import inventory from './inventory'
import logistic from './logistic'
import schedule from './schedule'
import members from './members'
import activityLogs from './activity-logs'

const management = {
    inventory: Object.assign(inventory, inventory),
    logistic: Object.assign(logistic, logistic),
    schedule: Object.assign(schedule, schedule),
    members: Object.assign(members, members),
    activityLogs: Object.assign(activityLogs, activityLogs),
}

export default management