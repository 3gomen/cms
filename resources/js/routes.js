import Dashboard from './components/Dashboard.vue';
import UserList from './pages/users/UserList.vue';
import AccessoryList from './pages/accessories/AccessoryList.vue';
import ClientList from './pages/clients/ClientList.vue';
import DeviceList from './pages/devices/DeviceList.vue';
import LocationList from './pages/locations/LocationList.vue';
import OrderList from './pages/orders/OrderList.vue';
import PartList from './pages/parts/PartList.vue';
import RepairList from './pages/repairs/RepairList.vue';
import SupplierList from './pages/suppliers/SupplierList.vue';

export default [
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: UserList,
    },
    {
        path: '/admin/accessories',
        name: 'admin.accessories',
        component: AccessoryList,
    },
    {
        path: '/admin/clients',
        name: 'admin.clients',
        component: ClientList,
    },
    {
        path: '/admin/repairs',
        name: 'admin.repairs',
        component: RepairList,
    },
    {
        path: '/admin/parts',
        name: 'admin.parts',
        component: PartList,
    },
    {
        path: '/admin/suppliers',
        name: 'admin.suppliers',
        component: SupplierList,
    },
    {
        path: '/admin/devices',
        name: 'admin.devices',
        component: DeviceList,
    },
    {
        path: '/admin/locations',
        name: 'admin.locations',
        component: LocationList,
    },
    {
        path: '/admin/orders',
        name: 'admin.orders',
        component: OrderList,
    },
]
