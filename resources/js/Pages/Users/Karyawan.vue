<template>
  <div>
    <Head title="Karyawan" />
    <div class="text-right">
      <transition name="fade">
        <button
          :class="selectedIds.length > 0 ? 'show' : ''"
          @click="deleteSelected"
          class="btn btn-danger mr-2 mb-2 fade"
        >
          <i class="fa fa-trash"></i>&nbsp;Delete Bulk
        </button>
      </transition>
      <button class="btn btn-primary mb-2" type="button" @click="createUser">
        + Data Baru
      </button>
    </div>
    <div
      v-if="$page.props.flash.success"
      class="alert alert-success"
      role="alert"
    >
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">
      {{ $page.props.flash.error }}
    </div>
    <div class="card shadow">
      <div class="card-header">
        <h4 class="card-title">
          <i class="fa fa-user"></i>&nbsp;Data Karyawan
        </h4>
      </div>
      <Table
        :columns="table"
        :rows="data"
        @update:selectedRows="handleSelectedRows"
      >
        <template #actions="{ row }">
          <button
            @click="editRow(row)"
            class="btn btn-warning btn-sm"
            title="Edit"
          >
            Edit</button
          >&nbsp;
          <button
            @click="deleteRow(row)"
            class="btn btn-danger btn-sm"
            title="Delete"
          >
            Delete
          </button>
        </template>
      </Table>
    </div>
    <Modal :show="modalShow" :title="modalTitle" id="modal-add">
      <form ref="formKaryawan" @submit.prevent="handleSubmit">
        <div class="row">
          <!-- ID Field (Hidden) -->
          <input type="hidden" v-model="form.id" />

          <!-- Name -->
          <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Name</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.name }"
              placeholder="Enter Name"
              required
            />
            <span class="text-danger">{{ $page?.props?.errors?.name }}</span>
          </div>

          <!-- NIK -->
          <div class="col-md-6 mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input
              type="text"
              id="nik"
              v-model="form.nik"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.nik }"
              placeholder="Enter NIK"
            />
            <span class="text-danger">{{ $page?.props?.errors?.nik }}</span>
          </div>

          <!-- Address -->
          <div class="col-md-12 mb-3">
            <label for="alamat" class="form-label">Address</label>
            <textarea
              id="alamat"
              v-model="form.alamat"
              class="form-control"
              rows="3"
              :class="{ 'is-invalid': $page?.props?.errors?.alamat }"
              placeholder="Enter Address"
            ></textarea>
            <span class="text-danger">{{ $page?.props?.errors?.alamat }}</span>
          </div>

          <!-- Phone -->
          <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input
              type="text"
              id="phone"
              v-model="form.phone"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.phone }"
              placeholder="Enter Phone Number"
            />
            <span class="text-danger">{{ $page?.props?.errors?.phone }}</span>
          </div>

          <!-- NPWP -->
          <div class="col-md-6 mb-3">
            <label for="npwp" class="form-label">NPWP</label>
            <input
              type="text"
              id="npwp"
              v-model="form.npwp"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.npwp }"
              placeholder="Enter NPWP"
            />
            <span class="text-danger">{{ $page?.props?.errors?.npwp }}</span>
          </div>

          <!-- Salary Per Day -->
          <div class="col-md-6 mb-3">
            <label for="salary_per_day" class="form-label"
              >Salary Per Day</label
            >
            <input
              type="number"
              id="salary_per_day"
              v-model="form.salary_per_day"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.salary_per_day }"
              placeholder="Enter Salary Per Day"
            />
            <span class="text-danger">{{
              $page?.props?.errors?.salary_per_day
            }}</span>
          </div>

          <!-- Salary Per Month -->
          <div class="col-md-6 mb-3">
            <label for="salary_per_month" class="form-label"
              >Salary Per Month</label
            >
            <input
              type="number"
              id="salary_per_month"
              v-model="form.salary_per_month"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.salary_per_month }"
              placeholder="Enter Salary Per Month"
            />
            <span class="text-danger">{{
              $page?.props?.errors?.salary_per_month
            }}</span>
          </div>

          <!-- Join Date -->
          <div class="col-md-6 mb-3">
            <label for="join_date" class="form-label">Join Date</label>
            <input
              type="date"
              id="join_date"
              v-model="form.join_date"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.join_date }"
            />
            <span class="text-danger">{{
              $page?.props?.errors?.join_date
            }}</span>
          </div>

          <!-- Leader ID -->
          <div class="col-md-6 mb-3">
            <label for="leader_id" class="form-label">Leader ID</label>
            <Multiselect
              v-model="form.leader_id"
              :options="karyawan"
              track-by="id"
              label="name"
              @search-change="getDataUsers"
              :internal-search="false"
              :class="{ 'is-invalid': $page?.props?.errors?.leader_id }"
            ></Multiselect>
            <span class="text-danger">{{
              $page?.props?.errors?.leader_id
            }}</span>
          </div>

          <!-- Store ID -->
          <div class="col-md-6 mb-3">
            <label for="toko_id" class="form-label">Store</label>
            <Multiselect
              v-model="form.toko_id"
              :options="store"
              track-by="id"
              label="name"
              @search-change="getDataStore"
              :internal-search="false"
              :class="{ 'is-invalid': $page?.props?.errors?.toko_id }"
            ></Multiselect>
            <span class="text-danger">{{ $page?.props?.errors?.toko_id }}</span>
          </div>

          <!-- Email -->
          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.email }"
              placeholder="Enter Email"
            />
            <span class="text-danger">{{ $page?.props?.errors?.email }}</span>
          </div>

          <!-- Is Account -->
          <div class="col-md-6 mb-3">
            <div class="form-check">
              <input
                type="checkbox"
                id="is_account"
                v-model="form.is_account"
                class="form-check-input"
                :class="{ 'is-invalid': $page?.props?.errors?.is_account }"
              />
              <label for="is_account" class="form-check-label"
                >Buatkan Akun?</label
              >
            </div>
            <span class="text-danger">{{
              $page?.props?.errors?.is_account
            }}</span>
          </div>

          <!-- Password -->
          <div class="col-md-6 mb-3 fade" :class="{ show: form.is_account }">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.password }"
              :placeholder="
                form.account ? 'Change or Let it empty' : 'Enter Password'
              "
            />
            <span class="text-danger">{{
              $page?.props?.errors?.password
            }}</span>
          </div>
        </div>
      </form>
      <template #footer>
        <button
          type="button"
          class="btn btn-primary"
          :class="{ disabled: form.submit }"
          @click="submitForm"
        >
          <template v-if="form.submit">
            <span
              class="spinner-border spinner-border-sm"
              role="status"
              aria-hidden="true"
            ></span>
            <span class="bott">Loading</span>
          </template>
          <span v-else class="bott">Save</span>
        </button>
      </template>
    </Modal>
  </div>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";
import Multiselect from "vue-multiselect";
import axios from "axios";
import "vue-multiselect/dist/vue-multiselect.css";
export default {
  layout: AuthenticatedLayout,
  props: ["users"],
  components: {
    Head,
    Modal,
    Multiselect,
  },
  data() {
    return {
      table: [
        {
          title: "Nama Karyawan",
          data: "name",
        },
        {
          title: "NIK",
          data: "nik",
        },
        {
          title: "Email",
          data: "email",
        },
        {
          title: "Nomor Handphone",
          data: "phone",
        },
        {
          title: "NPWP",
          data: "npwp",
        },
        {
          title: "Leader",
          data: "leader.name",
        },
        {
          title: "Store",
          data: "store.name",
        },
        {
          title: "Created At",
          data: "created_at",
        },
        {
          title: "Created By",
          data: "creator.name",
        },
      ],
      data: this.users,
      modalTitle: "",
      modalShow: false,
      selectedIds: [],
      form: {
        id: null,
        name: "",
        nik: "",
        alamat: "",
        phone: "",
        npwp: "",
        salary_per_day: "",
        salary_per_month: "",
        join_date: new Date().toISOString().substr(0, 10),
        leader_id: null,
        toko_id: null,
        jabatan: {},
        email: "",
        leader_id: "",
        is_account: false,
        password: "",
        submit: false,
        edit: false,
      },
      karyawan: [],
      store: [],
    };
  },
  mounted() {
    this.getDataUsers();
    this.getDataStore();
  },
  methods: {
    createUser() {
      this.$page.props.errors = null;
      this.resetForm();
      this.modalTitle = "Tambah Data Karyawan";
      $("#modal-add").modal("show");
    },
    editRow(row) {
      this.resetForm();
      this.populateForm(row);
      this.getDataUsers(row.leader?.name);
      this.getDataStore(row.store?.name);
      console.log(row);
      this.form.account = row.account;
      this.modalTitle = "Ubah Data Karyawan";
      $("#modal-add").modal("show");
    },
    deleteRow(row) {
      this.$confirm(
        "You won't be able to revert this!",
        () => this.deleteAction(row),
        false
      );
    },
    deleteAction(row) {
      router.delete(this.route('user.destroy', row.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.$success("Data berhasil dihapus");
          router.visit(this.$page.url, {
            only: ["users"],
          });
        },
        onError: () => {
          this.$error();
        },
      });
    },
    handleSelectedRows(selectedRows) {
      this.selectedIds = selectedRows;
    },
    async getDataUsers(val = "") {
      let { data } = await axios.get(this.route("users.get", {name: val}));
      this.karyawan = data;
    },
    async getDataStore(val = "") {
      let { data } = await axios.get(this.route("store.get", {name: val}));
      this.store = data;
    },
    async submitForm() {
      try {
        if (this.form.submit) {
          return;
        }

        this.form.submit = true;
        if (this.form.id == "" || this.form.id == null) {
          return router.post(this.route('users.store'), this.form, {
            preserveScroll: true,
            onSuccess: () => {
              this.form.submit = false;
              $("#modal-add").modal("hide");
              this.$success("Data berhasil disimpan");
              router.visit(this.$page.url, {
                only: ["users"],
              });
            },
            onError: () => {
              this.form.submit = false;
              this.$error();
            },
          });
        } else {
          return router.put(this.route('users.update', this.form.id), this.form, {
            preserveScroll: true,
            onSuccess: () => {
              this.form.submit = false;
              $("#modal-add").modal("hide");
              this.$success("Data berhasil disimpan");
              router.visit(this.$page.url, {
                only: ["users"],
              });
            },
            onError: () => {
              this.form.submit = false;
              this.$error();
            },
          });
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.form.submit = false;
      }
    },
    resetForm() {
      this.form = {
        id: null,
        name: "",
        nik: "",
        alamat: "",
        phone: "",
        npwp: "",
        salary_per_day: "",
        salary_per_month: "",
        join_date: new Date().toISOString().substr(0, 10),
        leader_id: null,
        toko_id: null,
        email: "",
        leader_id: "",
        is_account: false,
        password: "",
        submit: false,
        edit: false,
      };
    },
    populateForm(data) {
      this.form.id = data.id || null;
      this.form.name = data.name || "";
      this.form.nik = data.nik || "";
      this.form.alamat = data.address || "";
      this.form.phone = data.phone || "";
      this.form.npwp = data.npwp || "";
      this.form.salary_per_day = data.salary_per_day || "";
      this.form.salary_per_month = data.salary_per_month || "";
      this.form.join_date = data.join_date
        ? new Date(data.join_date).toISOString().substr(0, 10)
        : new Date().toISOString().substr(0, 10);
      this.form.leader_id = data.leader || null;
      this.form.toko_id = data.toko_id || null;
      this.form.email = data.email || "";
      this.form.is_account = !!data.account;
    },
  },
};
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0;
}
</style>
