<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import AccessoryListItem from './AccessoryListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr()
const accessories = ref({ 'data': [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);

const getAccessories = (page = 1) => {
    axios.get(`/api/accessories?page=${page}`, {
        params: {
            search: searchQuery.value,
        }
    }).then((response) => {
        accessories.value = response.data;
        selectedAccessories.value = [];
        selectAll.value = false;
    })
}

const createAccessorySchema = yup.object({
    category: yup.string().required(),
    brand: yup.string().required(),
    type: yup.string().required(),
    color: yup.string(),
    supplier_id: yup.number(),
    guarantee: yup.string().required(),
    price: yup.number().required(),
    sell_price: yup.number().required(),
    has_storage: yup.boolean(),
    comment: yup.string(),
});

const updateAccessorySchema = yup.object({
    category: yup.string().required(),
    brand: yup.string().required(),
    type: yup.string().required(),
    color: yup.string(),
    supplier_id: yup.number(),
    guarantee: yup.string().required(),
    price: yup.number().required(),
    sell_price: yup.number().required(),
    has_storage: yup.boolean(),
    comment: yup.string(),
});

const createAccessory = (values, { resetForm, setErrors }) => {
    axios.post('/api/accessories', values)
        .then((response) => {
            accessories.value.data.unshift(response.data);
            $('#accessoryFormModal').modal('hide');
            resetForm();
            toastr.success('Accessory created successfully!')
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
}

const updateAccessory = (values, { setErrors }) => {
    axios.put('/api/accessories/' + formValues.value.id, values)
        .then((response) => {
            const index = accessories.value.data.findIndex(accessory => accessory.id === response.data.id);
            accessories.value.data[index] = response.data;
            $('#accessoryFormModal').modal('hide');
            toastr.success(response.data.name + ' updated successfully!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
        });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateAccessory(values, actions);
    } else {
        createAccessory(values, actions);
    }
}

const accessoryBeingDeleted = ref({
    id: null,
});

const confirmAccessoryDeletion = (accessory) => {
    accessoryBeingDeleted.value.id = accessory.id;
    $('#deleteAccessoryModal').modal('show');
};

const deleteAccessory = () => {
    axios.delete(`/api/accessories/${accessoryBeingDeleted.value.id}`)
        .then(() => {
            $('#deleteAccessoryModal').modal('hide');
            toastr.success('Accessory deleted successfully!');
            accessories.value.data = accessories.value.data.filter(accessory => accessory.id !== accessoryBeingDeleted.value.id);
        });
};

const searchQuery = ref(null);

watch(searchQuery, debounce(() => {
    getAccessories();
}, 300));

const selectedAccessories = ref([]);
const toggleSelection = (accessory) => {
    const index = selectedAccessories.value.indexOf(accessory.id);
    if (index === -1) {
        selectedAccessories.value.push(accessory.id);
    } else {
        selectedAccessories.value.splice(index, 1);
    }
}

const bulkDelete = () => {
    axios.delete('/api/accessories', {
        data: {
            ids: selectedAccessories.value
        }
    })
        .then(response => {
            accessories.value.data = accessories.value.data.filter(accessory => !selectedAccessories.value.includes(accessory.id));
            selectedAccessories.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        })
}

const selectAll = ref(false);
const selectAllAccessories = () => {
    if (selectAll.value) {
        selectedAccessories.value = accessories.value.data.map(accessory => accessory.id);
    } else {
        selectedAccessories.value = [];
    }
}

const addAccessory = () => {
    editing.value = false;
    form.value.resetForm();
    formValues.value = {
        category: null,
        brand: null,
        type: null,
        color: null,
        supplier_id: null,
        guarantee: null,
        price: null,
        sell_price: null,
        has_storage: null,
        comment: null,
    };
    $('#accessoryFormModal').modal('show');
}

const editAccessory = (accessory) => {
    editing.value = true;
    form.value.resetForm();
    $('#accessoryFormModal').modal('show');
    formValues.value = {
        id: accessory.id,
        category: accessory.category,
        brand: accessory.brand,
        type: accessory.type,
        color: accessory.color,
        supplier_id: accessory.supplier_id,
        guarantee: accessory.guarantee,
        price: accessory.price,
        sell_price: accessory.sell_price,
        has_storage: accessory.has_storage,
        comment: accessory.comment,
    };
}

onMounted(() => {
    getAccessories();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Accessories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Accessories</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div>
                    <button @click="addAccessory" type="button" class="btn btn-primary mb-2">
                        <i class="fa fa-plus-circle mr-2"></i>Add New Accessory
                    </button>
                    <button v-if="selectedAccessories.length > 0" @click="bulkDelete" type="button"
                        class="btn btn-danger mb-2 ml-2">
                        <i class="fa fa-trash mr-2"></i>Delete Selected <span>({{ selectedAccessories.length }})</span>
                    </button>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-scroll">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" v-model="selectAll" @change="selectAllAccessories" /></th>
                                    <th style="width: 10px">ID</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>Color</th>
                                    <th>Supplier ID</th>
                                    <th>Guarantee</th>
                                    <th>Price</th>
                                    <th>Sell Price</th>
                                    <th>Has Storage</th>
                                    <th>Comment</th>
                                    <th>Created At</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody v-if="accessories.data.length > 0">
                                <AccessoryListItem v-for="(accessory, index) in accessories.data" :key="accessory.id"
                                    :accessory="accessory" :index="index"
                                    @confirm-accessory-deletion="confirmAccessoryDeletion" @edit-accessory="editAccessory"
                                    @accessory-deleted="accessoryDeleted" @toggle-selection="toggleSelection"
                                    :select-all="selectAll" />
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="14" class="text-center">No accessories found...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <Bootstrap4Pagination :data="accessories" @pagination-change-page="getAccessories" />
        </div>
    </div>

    <div class="modal fade" id="accessoryFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Accessory</span>
                        <span v-else>Add New Accessory</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit"
                    :validation-schema="editing ? updateAccessorySchema : createAccessorySchema" v-slot="{ errors }"
                    :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <Field name="category" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.category }" id="category" placeholder="Enter category" />
                            <span class="invalid-feedback">{{ errors.category }}</span>
                        </div>
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <Field name="brand" type="text" class="form-control" :class="{ 'is-invalid': errors.brand }"
                                id="brand" placeholder="Enter brand" />
                            <span class="invalid-feedback">{{ errors.brand }}</span>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <Field name="type" type="text" class="form-control" :class="{ 'is-invalid': errors.type }"
                                id="type" placeholder="Enter type" />
                            <span class="invalid-feedback">{{ errors.type }}</span>
                        </div>
                        <div class="form-group">
                            <label for="color">Color</label>
                            <Field name="color" type="text" class="form-control" :class="{ 'is-invalid': errors.color }"
                                id="color" placeholder="Enter color" />
                            <span class="invalid-feedback">{{ errors.color }}</span>
                        </div>
                        <div class="form-group">
                            <label for="supplier_id">Supplier ID</label>
                            <Field name="supplier_id" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.supplier_id }" id="supplier_id"
                                placeholder="Enter supplier ID" />
                            <span class="invalid-feedback">{{ errors.supplier_id }}</span>
                        </div>
                        <div class="form-group">
                            <label for="guarantee">Guarantee</label>
                            <Field name="guarantee" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.guarantee }" id="guarantee" placeholder="Enter guarantee" />
                            <span class="invalid-feedback">{{ errors.guarantee }}</span>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <Field name="price" type="number" class="form-control" :class="{ 'is-invalid': errors.price }"
                                id="price" placeholder="Enter price" />
                            <span class="invalid-feedback">{{ errors.price }}</span>
                        </div>
                        <div class="form-group">
                            <label for="sell_price">Sell Price</label>
                            <Field name="sell_price" type="number" class="form-control"
                                :class="{ 'is-invalid': errors.sell_price }" id="sell_price"
                                placeholder="Enter sell price" />
                            <span class="invalid-feedback">{{ errors.sell_price }}</span>
                        </div>
                        <div class="form-group row">
                            <label for="is_vip" class="col-sm-3 col-form-label">Has Storage</label>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <Field name="has_storage" type="checkbox" class="form-check-input"
                                        style="width: 1.25rem; height: 1.25rem;"
                                        :class="{ 'is-invalid': errors.has_storage }" id="has_storage" />
                                    <span class="invalid-feedback">{{ errors.has_storage }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <Field name="comment" type="text" class="form-control" :class="{ 'is-invalid': errors.comment }"
                                id="comment" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccessoryModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete Accessory</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this accessory?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                    <button @click="deleteAccessory" type="button" class="btn btn-danger">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
