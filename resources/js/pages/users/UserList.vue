<script setup>
import axios from 'axios';
import { onMounted, reactive, ref, watch } from 'vue';
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import UserListItem from './UserListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr()
const users = ref({ 'data': [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);

const getUsers = (page = 1) => {
    axios.get(`/api/users?page=${page}`, {
        params: {
            search: searchQuery.value,
        }
    }).then((response) => {
        users.value = response.data;
        selectedUsers.value = [];
        selectAll.value = false;
    })
}

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().required().email(),
    password: yup.string().required().min(8),
})

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().required().email(),
    password: yup.string().when((password, schema) => {
        return password ? schema.min(8) : schema;
    }),
})

const createUser = (values, { resetForm, setErrors }) => {
    axios.post('/api/users', values)
        .then((response) => {
            users.value.data.unshift(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success(response.data.name + ' created successfully!')
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
}

const updateUser = (values, { setErrors }) => {
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id === response.data.id);
            users.value[index] = response.data;
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success(response.data.name + ' updated successfully!');
        }).catch((error) => {
            setErrors(error.response.data.errors);
        });
}

const handleSubmit = (values, actions) => {
    console.log(actions);
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
}

const userBeingDeleted = reactive({
    id: null,
    name: null
});

const confirmUserDeletion = (user) => {
    userBeingDeleted.id = user.id;
    userBeingDeleted.name = user.name;
    $('#deleteUserModal').modal('show');
};

const deleteUser = () => {
    console.log(userBeingDeleted.id);
    axios.delete(`/api/users/${userBeingDeleted.id}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            toastr.success(userBeingDeleted.name + ' deleted successfully!');
            users.value.data = users.value.data.filter(user => user.id !== userBeingDeleted.id);
        });
};

const searchQuery = ref(null);

watch(searchQuery, debounce(() => {
    getUsers();
}, 300));

const selectedUsers = ref([]);
const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
    console.log(selectedUsers.value);
}

const bulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value
        }
    })
        .then(response => {
            users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
            selectedUsers.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        })
}

const selectAll = ref(false);
const selectAllUsers = () => {
    if (selectAll.value) {
        selectedUsers.value = users.value.data.map(user => user.id);
    } else {
        selectedUsers.value = [];
    }
    console.log(selectedUsers.value);
}

const addUser = () => {
    editing.value = false;
    form.value.resetForm();
    formValues.value = {
        name: null,
        email: null,
        password: null,
    };
    $('#userFormModal').modal('show');
}

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
}

onMounted(() => {
    getUsers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div>
                    <button @click="addUser" type="button" class="btn btn-primary mb-2">
                        <i class="fa fa-plus-circle mr-2"></i>Add New User
                    </button>
                    <button v-if="selectedUsers.length > 0" @click="bulkDelete" type="button"
                        class="btn btn-danger mb-2 ml-2">
                        <i class="fa fa-trash mr-2"></i>Delete Selected <span>({{ selectedUsers.length }})</span>
                    </button>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers" /></th>
                                <th style="width: 10px">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                            <UserListItem v-for="(user, index) in users.data" :key="user.id" :user=user :index=index
                                @confirm-user-deletion="confirmUserDeletion" @edit-user="editUser"
                                @user-deleted="userDeleted" @toggle-selection="toggleSelection" :select-all="selectAll" />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No users found...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers" />
        </div>
    </div>

    <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema"
                    v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                                id="name" aria-describedby="name" placeholder="Enter name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <Field name="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                                id="email" placeholder="Enter email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group">
                            <label for="password">
                                <span v-if="editing">New Password</span>
                                <span v-else>Password</span>
                            </label>
                            <Field name="password" type="password" class="form-control"
                                :class="{ 'is-invalid': errors.password }" id="password" placeholder="Password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
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
    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, cancel</button>
                    <button @click="deleteUser" type="button" class="btn btn-danger">Yes, delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
