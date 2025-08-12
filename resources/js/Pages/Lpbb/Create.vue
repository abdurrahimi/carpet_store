<template>
  <div>

    <Head :title="title ?? 'Create LPBB'" />
    <div v-if="$page.props.flash.success" class="alert alert-success" role="alert">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">
      {{ $page.props.flash.error }}
    </div>
    <div class="card shadow">
      <div class="card-header">
        <h4 class="card-title">
          <i class="fa fa-box"></i>&nbsp;Create LPBB
        </h4>
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="store_from">Store From<span class="text-danger">*</span></label>
            <Multiselect v-model="form.store_from" :options="store" label="name" placeholder="Select Store From"
              required>
            </Multiselect>
          </div>

          <div class="form-group">
            <label for="store_to">Store To<span class="text-danger">*</span></label>
            <Multiselect v-model="form.store_to" :options="store" label="name" placeholder="Select Store To" required />
          </div>
          <div class="form-group">
            <label for="products">Products<span class="text-danger">*</span></label>
            <Multiselect v-model="form.products" :options="product" :multiple="true" :close-on-select="false"
              :custom-label="customLabel" placeholder="Select Products" required track-by="id" />
          </div>
          <div class="form-group row">
            <div class="col-md-4 col-sm-12 col-xl-3 p-2" v-for="item in form.products" :key="item.id">
              <div class="card shadow">
                <img
                  :src="item.image ?? 'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'"
                  alt="Product Image" class="card-img-top"
                  @error="(e) => e.target.src = 'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'" />
                <div class="card-body">
                  <table class="table borderless">
                    <tr>
                      <td>Name</td>
                      <td>{{ item.design_name }}</td>
                    </tr>
                    <tr>
                      <td>Color</td>
                      <td>{{ item.color }}</td>
                    </tr>
                    <tr>
                      <td>
                        <strong>SKU:</strong>
                      </td>
                      <td><input type="number" v-model="item.total" class="form-control"></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Create</button>
        </form>
      </div>
    </div>
  </div>
</template>
<script lang="jsx">
export default {
  layout: AuthenticatedLayout
}
</script>
<script setup lang="jsx">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Multiselect from 'vue-multiselect';
import "vue-multiselect/dist/vue-multiselect.css";

const form = ref({
  store_from: {},
  store_to: {},
  products: []
});

const props = defineProps({
  title: String,
  data: Object,
  store: Object,
  product: Object,
});

const submitForm = () => {
  const payload = {
    store_from: form.value.store_from.id,
    store_to: form.value.store_to.id,
    products: form.value.products.map(product => ({
      id: product.id,
      total: product.total
    }))
  };

  router.post(route('lpbb.store'), payload, {
    onSuccess: () => {
      router.redirect(route('lpbb.index'));
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
    }
  });
};

const customLabel = ({ design_name, color }) => {
  if (color === null) {
    return design_name;
  }

  return `${design_name} - ${color}`;
}

</script>
