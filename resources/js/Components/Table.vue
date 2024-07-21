<template>
  <div>
    <div class="overflow-auto">
      <div class="d-flex justify-content-between pl-2 pt-2 pr-2 pb-0">
        <div class="form-group">
          <select
            class="form-control"
            v-model="page_size"
            @change="updateData(rows.first_page_url)"
          >
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        <div class="form-group">
          <input
            type="text"
            class="form-control"
            v-model="search_params"
            placeholder="Search... (Press Enter)"
            @keyup.enter="updateData(rows.first_page_url)"
          />
        </div>
      </div>
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>
              <input
                type="checkbox"
                v-model="selectAll"
                @change="toggleSelectAll"
              />
            </th>
            <th v-for="column in columns" :key="column">
              {{ column.title }}
            </th>
            <th v-if="$slots.actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <template v-if="rows.data.length > 0">
            <tr v-for="row in rows.data" :key="row.id">
              <td>
                <div class="input-group">
                  <input
                    type="checkbox"
                    :value="row.id"
                    v-model="selectedRows"
                    @change="emitSelectedRows"
                  />
                </div>
              </td>
              <td v-for="column in columns" :key="column.data">
                {{ getNestedValue(row, column.data) }}
              </td>
              <td v-if="$slots.actions">
                <!-- Use actions slot for custom rendering -->
                <slot name="actions" :row="row"></slot>
              </td>
            </tr>
          </template>

          <tr v-else>
            <td :colspan="columns.length + 2" class="text-center">
              No data found
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <nav v-if="rows" class="mb-2 mr-2">
      <ul class="pagination">
        <li
          class="page-item"
          v-for="(item, key) in rows.links"
          :key="key"
          :class="{ active: item.active, disabled: !item.url }"
        >
          <a
            class="page-link"
            href="#"
            @click.prevent="changePage(item.url)"
            v-html="item.label"
            :class="{ disabled: !item.url }"
          ></a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import { router } from "@inertiajs/vue3";
export default {
  name: "Table",
  props: {
    columns: {
      type: Array,
      required: true,
    },
    rows: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      selectedRows: [],
      selectAll: false,
      search_params: "",
      page_size: 10,
    };
  },
  computed: {
    totalPages() {
      return this.rows.last_page;
    },
  },
  mounted() {
    const url = this.$page.url;
    const queryParams = new URLSearchParams(url.split('?')[1]);
    const search = queryParams.get('search') || '';
    this.search_params = search;
    this.page_size = this.rows.per_page;
  },
  methods: {
    changePage(page) {
      router.get(page);
    },
    getNestedValue(obj, path) {
      return path.split(".").reduce((acc, part) => acc && acc[part], obj);
    },
    toggleSelectAll() {
      if (this.selectAll) {
        this.selectedRows = this.rows.data.map((row) => row.id);
      } else {
        this.selectedRows = [];
      }
      this.emitSelectedRows();
    },
    emitSelectedRows() {
      this.$emit("update:selectedRows", this.selectedRows);
    },
    updateData(url) {
      router.get(
        `${url}&search=${this.search_params}&page_size=${this.page_size}`
      );
    },
  },
  watch: {
    selectedRows(newVal) {
      this.selectAll = newVal.length === this.rows.data.length;
    },
  },
};
</script>
<style scoped>
.pagination {
  justify-content: right;
}

.page-item.disabled .page-link {
  pointer-events: none;
  opacity: 0.5;
}

.page-link {
  color: black;
}
</style>

