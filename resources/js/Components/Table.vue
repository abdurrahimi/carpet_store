<template>
  <div>
    <div>
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
        <slot name="filter"> </slot>
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
      <div class="overflow-auto continer-table" ref="tableContainer">
        <table class="table table-hover table-striped" ref="table">
          <thead>
            <tr>
              <th style="white-space: nowrap">
                <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
              </th>
              <th v-for="column in columns" :key="column" style="white-space: nowrap">
                {{ column.title }}
              </th>
              <th
                v-if="$slots.actions"
                class="sticky-column bg-white"
                style="white-space: nowrap"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody ref="tableBody">
            <template v-if="rows.data?.length > 0">
              <tr v-for="row in rows.data" :key="row.id" ref="trx">
                <td style="white-space: nowrap">
                  <div class="input-group">
                    <input
                      type="checkbox"
                      :value="row.id"
                      v-model="selectedRows"
                      @change="emitSelectedRows"
                    />
                  </div>
                </td>
                <td
                  v-for="column in columns"
                  :key="column.data"
                  style="white-space: nowrap"
                >

                  <component v-if="column.render && typeof column.render === 'function'" :is="column.render(row)"></component>
                  <template v-else>{{ getNestedValue(row, column.data) }}</template>
                </td>
                <td
                  v-if="$slots.actions"
                  style="white-space: nowrap"
                  class="sticky-column"
                  ref="stickyColumn"
                >
                  <!-- Use actions slot for custom rendering -->
                  <slot name="actions" :row="row"></slot>
                </td>
              </tr>
            </template>

            <tr v-else>
              <td :colspan="columns.length + 2" class="text-center">No data found</td>
            </tr>
          </tbody>
        </table>
      </div>
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

<script lang="jsx">
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
    const queryParams = new URLSearchParams(url.split("?")[1]);
    const search = queryParams.get("search") || "";
    this.search_params = search;
    this.page_size = this.rows.per_page;
    this.updateStickyColumnBackground();
    this.$refs.tableContainer.addEventListener(
      "scroll",
      this.updateStickyColumnBackground
    );
  },
  beforeDestroy() {
    this.$refs.tableContainer.removeEventListener(
      "scroll",
      this.updateStickyColumnBackground
    );
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
      router.get(`${url}&search=${this.search_params}&page_size=${this.page_size}`);
    },
    updateStickyColumnBackground() {
      const tableContainer = this.$refs.tableContainer;
      const scrollLeft = tableContainer.scrollLeft;
      const containerWidth = tableContainer.clientWidth;
      const tableWidth = tableContainer.scrollWidth;
      if(containerWidth == tableWidth) return
      // Update posisi sticky column
      const stickyColumnO = this.$refs.stickyColumn[0];
      const stickyColumnWidth = stickyColumnO.offsetWidth;
      const maxScrollLeft = tableWidth - containerWidth;
      window.x = this.$refs.table;
      this.$refs.stickyColumn.map((v, k) => {
        if (k % 2 == 0) {
          v.style.backgroundColor = `#f2f2f2`;
        } else {
          v.style.backgroundColor = `white`;
        }
        if (scrollLeft < maxScrollLeft - stickyColumnWidth) {
          v.style.left = `${scrollLeft}px`;
          v.style.boxShadow = `inset 10px 0 8px -8px rgba(0, 0, 0, 0.2)`;
        } else {
          v.style.left = `${maxScrollLeft}px`;
          v.style.boxShadow = `none`;
        }
      });
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

.sticky-column {
  position: sticky;
  right: 0px;
  /*background: inherit; /* optional, biar background column tetap sama */
  z-index: 1; /* optional, biar column di atas konten lain */
  transition: box-shadow 0.3s ease;
}
</style>
