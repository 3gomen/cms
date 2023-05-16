<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import SupplierListItem from './SupplierListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr()
const suppliers = ref({ 'data': [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);

const getSuppliers = (page = 1) => {
    axios.get(`/api/suppliers?page=${page}`, {
        params: {
            search: searchQuery.value,
        }
    }).then((response) => {
        suppliers.value = response.data;
        selectedSuppliers.value = [];
        selectAll.value = false;
    })
}

const createSupplierSchema = yup.object({
    name: yup.string().required(),
    website: yup.string(),
    email: yup.string().email().required(),
    contact_name: yup.string().required(),
    tel: yup.string().required(),
});

const updateSupplierSchema = yup.object({
    name: yup.string().required(),
    website: yup.string(),
    email: yup.string().email().required(),
    contact_name: yup.string().required(),
    tel: yup.string().required(),
});

const createSupplier = (values, { resetForm, setErrors }) => {
    axios.post('/api/suppliers', values)
        .then((response) => {
            suppliers.value.data.unshift(response.data);
            $('#supplierFormModal').modal('hide');
            resetForm();
            toastr.success(response.data.name + ' created successfully!')
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
}

const updateSupplier = (values, { resetForm, setErrors }) => {
    axios.put('/api/suppliers/' + formValues.value.id, values)
        .then((response) => {
            const index = suppliers.value.data.findIndex(supplier => supplier.id === response.data.id);
            suppliers.value.data[index] = response.data;
            $('#supplierFormModal').modal('hide');
            resetForm();
            toastr.success(response.data.name + ' updated successfully!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
        });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateSupplier(values, actions);
    } else {
        createSupplier(values, actions);
    }
}

const supplierBeingDeleted = ref({
    id: null,
});

const confirmSupplierDeletion = (supplier) => {
    supplierBeingDeleted.value.id = supplier.id;
    $('#deleteSupplierModal').modal('show');
};

const deleteSupplier = () => {
    axios.delete(`/api/suppliers/${supplierBeingDeleted.value.id}`)
        .then(() => {
            $('#deleteSupplierModal').modal('hide');
            toastr.success('Supplier deleted successfully!');
            suppliers.value.data = suppliers.value.data.filter(supplier => supplier.id !== supplierBeingDeleted.value.id);
        });
};

const searchQuery = ref(null);

watch(searchQuery, debounce(() => {
    getSuppliers();
}, 300));

const selectedSuppliers = ref([]);
const toggleSelection = (supplier) => {
    const index = selectedSuppliers.value.indexOf(supplier.id);
    if (index === -1) {
        selectedSuppliers.value.push(supplier.id);
    } else {
        selectedSuppliers.value.splice(index, 1);
    }
}

const bulkDelete = () => {
    axios.delete('/api/suppliers', {
        data: {
            ids: selectedSuppliers.value
        }
    })
        .then(response => {
            suppliers.value.data = suppliers.value.data.filter(supplier => !selectedSuppliers.value.includes(supplier.id));
            selectedSuppliers.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        })
}

const selectAll = ref(false);
const selectAllSuppliers = () => {
    if (selectAll.value) {
        selectedSuppliers.value = suppliers.value.data.map(supplier => supplier.id);
    } else {
        selectedSuppliers.value = [];
    }
}

const addSupplier = () => {
    editing.value = false;
    form.value.resetForm();
    formValues.value = {
        id: null,
        name: '',
        website: '',
        email: '',
        contact_name: '',
        tel: '',
    };
    $('#supplierFormModal').modal('show');
}

const editSupplier = (supplier) => {
    editing.value = true;
    form.value.resetForm();
    $('#supplierFormModal').modal('show');
    formValues.value = {
        id: supplier.id,
        name: supplier.name,
        website: supplier.website,
        email: supplier.email,
        contact_name: supplier.contact_name,
        tel: supplier.tel,
    };
}

onMounted(() => {
    getSuppliers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Suppliers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Suppliers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div>
                    <button @click="addSupplier" type="button" class="btn btn-primary mb-2">
                        <i class="fa fa-plus-circle mr-2"></i>Add New Supplier
                    </button>
                    <button v-if="selectedSuppliers.length > 0" @click="bulkDelete" type="button"
                        class="btn btn-danger mb-2 ml-2">
                        <i class="fa fa-trash mr-2"></i>Delete Selected <span>({{ selectedSuppliers.length }})</span>
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
                                    <th><input type="checkbox" v-model="selectAll" @change="selectAllSuppliers" /></th>
                                    <th style="width: 10px">ID</th>
                                    <th>Name</th>
                                    <th>Website</th>
                                    <th>Email</th>
                                    <th>Contact Name</th>
                                    <th>Tel</th>
                                    <th>Created At</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody v-if="suppliers.data.length > 0">
                                <SupplierListItem v-for="(supplier, index) in suppliers.data" :key="supplier.id"
                                    :supplier="supplier" :index="index" @confirm-supplier-deletion="confirmSupplierDeletion"
                                    @edit-supplier="editSupplier" @supplier-deleted="supplierDeleted"
                                    @toggle-selection="toggleSelection" :select-all="selectAll" />
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="9" class="text-center">No suppliers found...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <Bootstrap4Pagination :data="suppliers" @pagination-change-page="getSuppliers" />
        </div>
    </div>
    <div class="modal fade" id="supplierFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Supplier</span>
                        <span v-else>Add New Supplier</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit"
                    :validation-schema="editing ? updateSupplierSchema : createSupplierSchema" v-slot="{ errors }"
                    :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                id="name" placeholder="Enter name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <Field name="website" type="text" class="form-control" :class="{ 'is-invalid': errors.website }"
                                id="website" placeholder="Enter website" />
                            <span class="invalid-feedback">{{ errors.website }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="text" class="form-control" :class="{ 'is-invalid': errors.email }"
                                id="email" placeholder="Enter email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group">
                            <label for="contact_name">Contact Name</label>
                            <Field name="contact_name" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.contact_name }" id="contact_name"
                                placeholder="Enter contact name" />
                            <span class="invalid-feedback">{{ errors.contact_name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="tel">Telephone</label>
                            <Field name="tel" type="text" class="form-control" :class="{ 'is-invalid': errors.tel }"
                                id="tel" placeholder="Enter telephone" />
                            <span class="invalid-feedback">{{ errors.tel }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <span v-if="editing">Update</span>
                            <span v-else>Create</span>
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="supplierFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Supplier</span>
                        <span v-else>Add New Supplier</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit"
                    :validation-schema="editing ? updateSupplierSchema : createSupplierSchema" v-slot="{ errors }"
                    :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                id="name" placeholder="Enter name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <Field name="website" type="text" class="form-control" :class="{ 'is-invalid': errors.website }"
                                id="website" placeholder="Enter website" />
                            <span class="invalid-feedback">{{ errors.website }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="text" class="form-control" :class="{ 'is-invalid': errors.email }"
                                id="email" placeholder="Enter email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group">
                            <label for="contact_name">Contact Name</label>
                            <Field name="contact_name" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.contact_name }" id="contact_name"
                                placeholder="Enter contact name" />
                            <span class="invalid-feedback">{{ errors.contact_name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="tel">Telephone</label>
                            <Field name="tel" type="text" class="form-control" :class="{ 'is-invalid': errors.tel }"
                                id="tel" placeholder="Enter telephone" />
                            <span class="invalid-feedback">{{ errors.tel }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteSupplierModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete Supplier</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this supplier?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                    <button @click="deleteSupplier" type="button" class="btn btn-danger">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

