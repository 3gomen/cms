<script setup>
import { formatDate } from '../../helper.js';
import { useToastr } from '../../toastr.js';
import { ref } from 'vue';

const toastr = useToastr();

const props = defineProps({
    accessory: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['accessoryDeleted', 'editAccessory', 'confirmAccessoryDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.accessory);
}
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></td>
        <td>{{ accessory.id }}</td>
        <td>{{ accessory.category }}</td>
        <td>{{ accessory.brand }}</td>
        <td>{{ accessory.type }}</td>
        <td>{{ accessory.color }}</td>
        <td>{{ accessory.supplier.name }}</td>
        <td>{{ accessory.guarantee }}</td>
        <td>{{ accessory.price }}</td>
        <td>{{ accessory.sell_price }}</td>
        <td>{{ accessory.has_storage }}</td>
        <td>{{ accessory.comment }}</td>
        <td>{{ formatDate(accessory.created_at) }}</td>
        <td>
            <a href="#" @click.prevent="$emit('editAccessory', accessory)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$emit('confirmAccessoryDeletion', accessory)"><i
                    class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>
