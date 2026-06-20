import inventory from './inventory'
import logistic from './logistic'
import schedule from './schedule'
import members from './members'

const management = {
    inventory: Object.assign(inventory, inventory),
    logistic: Object.assign(logistic, logistic),
    schedule: Object.assign(schedule, schedule),
    members: Object.assign(members, members),
}

export default management