<script setup>
import { formatDate } from '../../helper.js';
import { useToastr } from '../../toastr.js';
import { ref } from 'vue';

const toastr = useToastr()

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion']);

const roles = ref([
    {
        name: 'ADMIN',
        value: 1,
    },
    {
        name: 'USER',
        value: 2,
    },
]);

const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
        .then((response) => {
            toastr.success(user.name + ' role updated successfully!');
        });
}

const toggleSelection = () => {
    emit('toggleSelection', props.user);
}

</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ formatDate(user.created_at) }}</td>
        <td>
            <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="user.role === role.name">{{ role.name }}
                </option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmUserDeletion', user)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
