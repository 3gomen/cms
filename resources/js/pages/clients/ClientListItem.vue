<script setup>
import { formatDate } from '../../helper.js';
import { useToastr } from '../../toastr.js';
import { ref, defineProps, defineEmits } from 'vue';

const toastr = useToastr();

const props = defineProps({
    client: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['clientDeleted', 'editClient', 'confirmClientDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.client);
}
</script>

<template>
    <tr :class="{ 'table-warning': client.is_vip }">
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ client.id }}</td>
        <td>{{ client.name }}</td>
        <td>{{ client.tel }}</td>
        <td>{{ client.email }}</td>
        <td>
            <span v-if="client.is_vip" class="badge badge-warning">VIP</span>
            <span v-else></span>
        </td>
        <td>{{ client.address }}</td>
        <td>{{ client.birthinfo }}</td>
        <td>{{ client.idcard }}</td>
        <td>{{ client.comment }}</td>
        <td>{{ formatDate(client.created_at) }}</td>
        <td>
            <a href="#" @click.prevent="$emit('editClient', client)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmClientDeletion', client)"><i
                    class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
