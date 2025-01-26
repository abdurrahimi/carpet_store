<template>
  <div>

    <Head title="Customer" />
    <div class="text-right">
      <transition name="fade">
        <button :class="selectedIds.length > 0 ? 'show' : ''" @click="deleteSelected"
          class="btn btn-danger mr-2 mb-2 fade">
          <i class="fa fa-trash"></i>&nbsp;Delete Bulk
        </button>
      </transition>
      <button class="btn btn-primary mb-2" type="button" @click="createUser">
        + Data Baru
      </button>
    </div>
    <div v-if="$page.props.flash.success" class="alert alert-success" role="alert">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">
      {{ $page.props.flash.error }}
    </div>
    <div class="card shadow">
      <div class="card-header">
        <h4 class="card-title">
          <i class="fa fa-user"></i>&nbsp;Data Customer
        </h4>
      </div>

      <Table :columns="table" :rows="data" @update:selectedRows="handleSelectedRows">
        <template #actions="{ row }">
          <button @click="editRow(row)" class="btn btn-warning btn-sm" title="Edit">
            Edit</button>&nbsp;
          <button @click="deleteRow(row)" class="btn btn-danger btn-sm" title="Delete">
            Delete
          </button>
        </template>
      </Table>

      <Modal :show="modalShow" :title="modalTitle" id="modal-add">
        <form ref="formCustomer" @submit.prevent="submitForm">
          <div class="row">
            <!-- ID Field (Hidden) -->
            <input type="hidden" v-model="form.id" />

            <!-- Name -->
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" id="name" v-model="form.name" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.name }" placeholder="Enter Name" required />
              <span class="text-danger">{{ $page?.props?.errors?.name }}</span>
            </div>

            <!-- Phone -->
            <div class="col-md-6 mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="text" id="phone" v-model="form.phone" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.phone }" placeholder="Enter Phone" />
              <span class="text-danger">{{ $page?.props?.errors?.phone }}</span>
            </div>

            <!-- Title -->
            <div class="col-md-6 mb-3">
              <label for="title" class="form-label">Title</label>
              <input type="text" id="title" v-model="form.title" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.title }" placeholder="Enter Title" />
              <span class="text-danger">{{ $page?.props?.errors?.title }}</span>
            </div>

            <!-- Address -->
            <div class="col-md-12 mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea id="address" v-model="form.address" class="form-control" rows="3"
                :class="{ 'is-invalid': $page?.props?.errors?.address }" placeholder="Enter Address"></textarea>
              <span class="text-danger">{{ $page?.props?.errors?.address }}</span>
            </div>

            <!-- NPWP -->
            <div class="col-md-6 mb-3">
              <label for="npwp" class="form-label">NPWP</label>
              <input type="text" id="npwp" v-model="form.npwp" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.npwp }" placeholder="Enter NPWP" />
              <span class="text-danger">{{ $page?.props?.errors?.npwp }}</span>
            </div>

            <!-- ID Number -->
            <div class="col-md-6 mb-3">
              <label for="id_no" class="form-label">ID Number</label>
              <input type="number" id="id_no" v-model="form.id_no" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.id_no }" placeholder="Enter ID Number" />
              <span class="text-danger">{{ $page?.props?.errors?.id_no }}</span>
            </div>

            <!-- ID Image -->
            <div class="col-md-6 mb-3">
              <label for="id_img" class="form-label">ID Image</label>
              <input type="file" id="id_img" @change="handleFileUpload" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.id_img }" />
              <span class="text-danger">{{ $page?.props?.errors?.id_img }}</span>
            </div>

            <!-- Type -->
            <div class="col-md-6 mb-3">
              <label for="type" class="form-label">Customer Type</label>
              <select id="type" v-model="form.type" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.type }">
                <option value="">Select Type</option>
                <option value="1">Reseller Lama</option>
                <option value="2">Reseller Baru</option>
                <option value="9">Retail</option>
              </select>
              <span class="text-danger">{{ $page?.props?.errors?.type }}</span>
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" v-model="form.email" class="form-control"
                :class="{ 'is-invalid': $page?.props?.errors?.email }" placeholder="Enter Email" />
              <span class="text-danger">{{ $page?.props?.errors?.email }}</span>
            </div>
          </div>
        </form>

        <template #footer>
          <button type="button" class="btn btn-primary" :class="{ disabled: form.submit }" @click="submitForm">
            <template v-if="form.submit">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <span class="bott">Loading</span>
            </template>
            <span v-else class="bott">Save</span>
          </button>
        </template>
      </Modal>
    </div>
  </div>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.css";
export default {
  layout: AuthenticatedLayout,
  props: ["customer"],
  components: {
    Head,
    Modal,
    Multiselect,
  },
  data() {
    return {
      table: [
        {
          title: "Nama Customer",
          data: "name",
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
          title: "Created At",
          data: "created_at",
        },
        {
          title: "Created By",
          data: "creator.name",
        },
      ],
      data: this.customer,
      selectedIds: [],
      form: {},
      stores: [],
      modalShow: false,
      modalTitle: "",
      errors: {},
    }
  },
  mounted() {

  },
  methods: {
    createUser() {
      this.$page.props.errors = null;
      this.resetForm();
      this.modalTitle = "Tambah Data Pelanggan";
      $("#modal-add").modal("show");
    },
    editRow(row) {
      this.$page.props.errors = null;
      this.resetForm(row);
      this.modalTitle = "Ubah Data Pelanggan";
      $("#modal-add").modal("show");
    },
    async submitForm() {
      if (this.form.submit) {
        return;
      }

      this.form.submit = true;
      router.post(
        this.form.id
          ? this.route('customer.store')
          : this.route('customer.update', this.form.id),
        this.form,
        {
          preserveScroll: true,
          onSuccess: () => {
            this.form.submit = false;
            $("#modal-add").modal("hide");
            this.$success("Data berhasil dihapus");
            router.visit(this.$page.url, {
              only: ["users"],
            });
          },
          onError: () => {
            this.form.submit = false;
            this.$error();
          },
        });
    },
    deleteRow(row) {
      this.$confirm(
        "You won't be able to revert this!",
        () => this.deleteAction(row),
        false
      );
    },
    deleteAction(row) {
      router.post(this.route('customer.delete'), { ids: [row.id] }, {
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
    deleteSelected() {
      this.$confirm(
        "You won't be able to revert this!",
        () => this.deleteSelectedAction(),
        false
      );
    },
    deleteSelectedAction() {
      router.post(this.route('customer.delete'), { ids: this.selectedIds }, {
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
    resetForm(data = null) {
      this.form = {
        id: data?.id ?? "",
        name: data?.name ?? "",
        phone: data?.phone ?? "",
        title: data?.title ?? "",
        address: data?.address ?? "",
        npwp: data?.npwp ?? "",
        id_no: data?.id_no ?? "",
        id_img: data?.id_img ?? "",
        type: data?.type ?? "",
        email: data?.email ?? "",
      }
    }
  },
};
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to

/* .fade-leave-active in <2.1.8 */
  {
  opacity: 0;
}
</style>