<script setup>
import axios from 'axios';
import { onMounted, ref, watch, nextTick } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import ClientListItem from './ClientListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr()
const clients = ref({ 'data': [] });
const editing = ref(false);
const formValues = ref({
    name: "",
    tel: "",
    email: "",
    is_vip: false,
    address: "",
    birthinfo: "",
    idcard: "",
    comment: "",
});
const form = ref(null);

const getClients = (page = 1) => {
    axios.get(`/api/clients?page=${page}`, {
        params: {
            search: searchQuery.value,
        }
    }).then((response) => {
        clients.value = response.data;
        selectedClients.value = [];
        selectAll.value = false;
    })
}

const createClientSchema = yup.object({
    name: yup.string().required(),
    tel: yup.string(),
    email: yup.string().email(),
    is_vip: yup.boolean(),
    address: yup.string(),
    birthinfo: yup.string(),
    idcard: yup.string(),
    comment: yup.string(),
});

const updateClientSchema = yup.object({
    name: yup.string().required(),
    tel: yup.string(),
    email: yup.string().email(),
    is_vip: yup.boolean(),
    address: yup.string(),
    birthinfo: yup.string(),
    idcard: yup.string(),
    comment: yup.string(),
});

const createClient = (values, { resetForm, setErrors }) => {
    axios.post('/api/clients', values)
        .then((response) => {
            console.log(response.data);
            clients.value.data.unshift(response.data);
            $('#clientFormModal').modal('hide');
            resetForm();
            toastr.success('Client created successfully!')
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
}

const updateClient = (values, { setErrors }) => {
    axios.put('/api/clients/' + formValues.value.id, values)
        .then((response) => {
            const index = clients.value.data.findIndex(client => client.id === response.data.id);
            clients.value.data[index] = response.data;
            $('#clientFormModal').modal('hide');
            toastr.success(response.data.name + ' updated successfully!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
        });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateClient(values, actions);
    } else {
        console.log(values);
        createClient(values, actions);
    }
}

const clientBeingDeleted = ref({
    id: null,
});

const confirmClientDeletion = (client) => {
    clientBeingDeleted.value.id = client.id;
    $('#deleteClientModal').modal('show');
};

const deleteClient = () => {
    axios.delete(`/api/clients/${clientBeingDeleted.value.id}`)
        .then(() => {
            $('#deleteClientModal').modal('hide');
            toastr.success('Client deleted successfully!');
            clients.value.data = clients.value.data.filter(client => client.id !== clientBeingDeleted.value.id);
        });
};

const searchQuery = ref(null);

watch(searchQuery, debounce(() => {
    getClients();
}, 300));

const selectedClients = ref([]);
const toggleSelection = (client) => {
    const index = selectedClients.value.indexOf(client.id);
    if (index === -1) {
        selectedClients.value.push(client.id);
    } else {
        selectedClients.value.splice(index, 1);
    }
}

const bulkDelete = () => {
    axios.delete('/api/clients', {
        data: {
            ids: selectedClients.value
        }
    })
        .then(response => {
            clients.value.data = clients.value.data.filter(client => !selectedClients.value.includes(client.id));
            selectedClients.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        })
}

const selectAll = ref(false);
const selectAllClients = () => {
    if (selectAll.value) {
        selectedClients.value = clients.value.data.map(client => client.id);
    } else {
        selectedClients.value = [];
    }
}

const addClient = () => {
    console.log(formValues)
    editing.value = false;
    form.value.resetForm();
    formValues.value = {
        name: "",
        tel: "",
        email: "",
        is_vip: false,
        address: "",
        birthinfo: "",
        idcard: "",
        comment: "",
    };
    nextTick(() => {
        $('#clientFormModal').modal('show');
    });
}

const editClient = (client) => {
    editing.value = true;
    form.value.resetForm();
    formValues.value = {
        name: client.name || "",
        tel: client.tel || "",
        email: client.email || "",
        is_vip: client.is_vip || false,
        address: client.address || "",
        birthinfo: client.birthinfo || "",
        idcard: client.idcard || "",
        comment: client.comment || "",
    };
    nextTick(() => {
        $('#clientFormModal').modal('show');
    });
}

onMounted(() => {
    getClients();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Clients</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Clients</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div>
                    <button @click="addClient" type="button" class="btn btn-primary mb-2">
                        <i class="fa fa-plus-circle mr-2"></i>Add New Client
                    </button>
                    <button v-if="selectedClients.length > 0" @click="bulkDelete" type="button"
                        class="btn btn-danger mb-2 ml-2">
                        <i class="fa fa-trash mr-2"></i>Delete Selected <span>({{ selectedClients.length }})</span>
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
                                    <th><input type="checkbox" v-model="selectAll" @change="selectAllClients" /></th>
                                    <th style="width: 10px">ID</th>
                                    <th>Name</th>
                                    <th>Tel</th>
                                    <th>Email</th>
                                    <th>VIP</th>
                                    <th>Address</th>
                                    <th>Birth Info</th>
                                    <th>ID Card</th>
                                    <th>Comment</th>
                                    <th>Created At</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody v-if="clients.data.length > 0">
                                <ClientListItem v-for="(client, index) in clients.data" :key="client.id" :client="client"
                                    :index="index" @confirm-client-deletion="confirmClientDeletion"
                                    @edit-client="editClient" @client-deleted="clientDeleted"
                                    @toggle-selection="toggleSelection" :select-all="selectAll" />
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="12" class="text-center">No clients found...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <Bootstrap4Pagination :data="clients" @pagination-change-page="getClients" />
        </div>
    </div>
    <div class="modal fade" id="clientFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit Client</span>
                        <span v-else>Add New Client</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit"
                    :validation-schema="editing ? updateClientSchema : createClientSchema" v-slot="{ errors }"
                    :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                id="name" placeholder="Enter name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="tel">Tel</label>
                            <Field name="tel" type="text" class="form-control" :class="{ 'is-invalid': errors.tel }"
                                id="tel" placeholder="Enter telephone" />
                            <span class="invalid-feedback">{{ errors.tel }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                                id="email" placeholder="Enter email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group row">
                            <label for="is_vip" class="col-sm-3 col-form-label">VIP</label>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <Field name="is_vip" value=1 type="checkbox" class="form-control"
                                        style="width: 1.25rem; height: 1.25rem;" :class="{ 'is-invalid': errors.is_vip }"
                                        id="is_vip" v-model="formValues.is_vip" />
                                </div>
                                <span class="invalid-feedback">{{ errors.is_vip }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <Field name="address" type="text" class="form-control" :class="{ 'is-invalid': errors.address }"
                                id="address" placeholder="Enter address" />
                            <span class="invalid-feedback">{{ errors.address }}</span>
                        </div>
                        <div class="form-group">
                            <label for="birthinfo">Birth Info</label>
                            <Field name="birthinfo" type="text" class="form-control"
                                :class="{ 'is-invalid': errors.birthinfo }" id="birthinfo"
                                placeholder="Enter birth information" />
                            <span class="invalid-feedback">{{ errors.birthinfo }}</span>
                        </div>
                        <div class="form-group">
                            <label for="idcard">ID Card</label>
                            <Field name="idcard" type="text" class="form-control" :class="{ 'is-invalid': errors.idcard }"
                                id="idcard" placeholder="Enter ID card" />
                            <span class="invalid-feedback">{{ errors.idcard }}</span>
                        </div>
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <Field name="comment" type="text" class="form-control" :class="{ 'is-invalid': errors.comment }"
                                id="comment" />
                            <span class="invalid-feedback">{{ errors.comment }}</span>
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

    <div class="modal fade" id="deleteClientModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete Client</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this client?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                    <button @click="deleteClient" type="button" class="btn btn-danger">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

