<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>
<template>
    <div>
        <Head title="Dashboard" />

        <AuthenticatedLayout>
            <div class="card" style="height:200vh">
                <div class="card-body shadow mb-4">
                    <Table :columns="table" :rows="data"/>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>

</template>
<script>
import Table from "@/Components/Table.vue";
export default {
    components: {
        Table
    },
    data(){
        return {
            table: [
                {
                    title: "Email",
                    data: "email"
                },
                {
                    title: "Created At",
                    data: "created_at"
                },
                {
                    title: "Created By",
                    data: "created_by"
                }
            ],
            data: []

        }
    }
}
</script>
