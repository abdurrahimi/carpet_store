<template>
    <div>
      <Head title="Data Toko" />
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
            <i class="fa fa-user"></i>&nbsp;Data Toko
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
