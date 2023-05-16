<script setup>
import { formatDate } from '../../helper.js';
import { useToastr } from '../../toastr.js';
import { ref } from 'vue';

const toastr = useToastr();

const props = defineProps({
    supplier: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['supplierDeleted', 'editSupplier', 'confirmSupplierDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.supplier);
}
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ supplier.id }}</td>
        <td>{{ supplier.name }}</td>
        <td>{{ supplier.website }}</td>
        <td>{{ supplier.email }}</td>
        <td>{{ supplier.contact_name }}</td>
        <td>{{ supplier.tel }}</td>
        <td>{{ formatDate(supplier.created_at) }}</td>
        <td>
            <a href="#" @click.prevent="$emit('editSupplier', supplier)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmSupplierDeletion', supplier)"><i
                    class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
