<template>
    <div>
      <Head title="Customer" />
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
            <i class="fa fa-user"></i>&nbsp;Data Customer
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
      }
    },
    mounted() {
      
    },
    methods: {
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
  