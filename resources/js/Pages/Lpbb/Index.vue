<script setup lang="jsx">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";
import "vue-multiselect/dist/vue-multiselect.css";
import { ref, onMounted } from 'vue';
import axios from "axios";


const props = defineProps({
  auth: Object,
  errors: Object,
  flash: Object,
  stock: Object,
});

const createLpbb = () => {
  router.visit('/lpbb/create');
};

const table = ref([
  { data: 'created_at', title: 'Tanggal' },
  { data: 'id', title: 'Nomor LPBB' },
  { data: 'from_store.name', title: 'From Store' },
  { data: 'to_store.name', title: 'To Store' },
  {
    data: 'status', title: 'Status',
    render: (row) => {
      if (row.status == 0) {
        return <span class={'badge badge-warning'}>Menunggu Approval</span>
      } else if (row.status == 1) {
        return <span class={'badge badge-success'}>Disetujui</span>
      } else {
        return <span class={'badge badge-danger'}>Ditolak</span>
      }
    }
  },
  {
    data: 'aksi',
    title: 'Aksi',
    render: (row) => {
      return (
        <div>
          <button class="btn btn-primary btn-sm" onClick="viewLpbb('${row.nomor_lpbb}')">
            <i class="fa fa-print"></i> Cetak
          </button> &nbsp;
          <button class="btn btn-success btn-sm" onClick="approveLpbb('${row.nomor_lpbb}')" >
            <i class="fa fa-check"></i> Lihat Approval
          </button >
        </div >
      );
    }

  }
]);
const data = ref([]);

const fetchLpbbData = async () => {
  try {
    const response = await axios.get(route('lpbb.data'));
    data.value = response.data;
  } catch (error) {
    console.error('Error fetching LPBB data:', error);
  }
};

onMounted(() => {
  // Fetch LPBB data
  fetchLpbbData();
});
</script>
<template>
  <div>

    <Head title="Data LPBB" />
    <div class="text-right">
      <button class="btn btn-primary mb-2" type="button" @click="createLpbb">
        + LPBB Baru
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
          <i class="fa fa-box"></i>&nbsp;Data LPBB
        </h4>
      </div>
      <div class="card-body">
        <Table :columns="table" :rows="data" />
      </div>
    </div>
  </div>
</template>
<script lang="jsx">


export default {
  layout: AuthenticatedLayout,
}
</script>