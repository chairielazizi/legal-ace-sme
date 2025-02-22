<template>
    <Head title="Operational Cost" />

    <div class="flex flex-col flex-1">
        <main class="h-full pb-16 overflow-y-auto mx-3 my-4">
            <Head :title="page_title" />

            <page-heading
                :page_title="page_title"
                :page_subtitle="page_subtitle"
                :breadcrumbs="breadcrumbs"
            />

            <div class="container px-6 mx-auto grid">
                <!-- <h4 class="my-6 text-2xl font-semibold">Non-Recurring</h4> -->

                <div class="flex items-center justify-between mb-4">
                    <search-filter
                        v-model="form.search"
                        class="mr-4 w-full max-w-md"
                        @reset="reset"
                    ></search-filter>
                    <Link href="/lawyer/operational-cost/create">
                        <button
                            class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-800 border border-transparent rounded-lg active:bg-blue-900 hover:bg-blue-900 focus:outline-none focus:shadow-outline-blue"
                        >
                            Add Expense
                        </button>
                    </Link>
                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-3">DATE</th>
                                <th scope="col" class="px-6 py-3">
                                    DESCRIPTION
                                </th>
                                <th scope="col" class="px-6 py-3">ACCOUNT</th>
                                <th scope="col" class="px-6 py-3">AMOUNT</th>
                                <!-- <th scope="col" class="px-6 py-3">
                                    Balance
                                </th> -->
                                <th scope="col" class="px-6 py-3">
                                    ACTION
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="cost in non_recurring.data"
                                :key="cost.id"
                                class="bg-white border-b"
                            >
                                <th
                                    scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                >
                                    {{ cost.date }}
                                </th>
                                <th
                                    scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                >
                                    {{ formatString(cost.details) }}
                                </th>
                                <th
                                    scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                >
                                    {{ cost.label }}
                                </th>
                                <th
                                    scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                >
                                    RM {{ formatToTwoDecimal(cost.amount) }}
                                </th>
                                <!-- <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ cost.balance }}
                                </th> -->
                                <td class="px-6 py-4 text-left">
                                    <Link
                                        :href="`/lawyer/operational-cost/${cost.id}/view`"
                                        class="font-medium text-blue-600 hover:underline p-1"
                                        >View
                                    </Link>
                                    <Link
                                        :href="`/lawyer/operational-cost/${cost.id}/edit`"
                                        class="font-medium text-blue-600 hover:underline"
                                        >Edit</Link
                                    >
                                    <button
                                        @click="showDeleteConfirmation(cost)"
                                        as="button"
                                        class="ml-3 font-medium text-red-600 hover:underline"
                                        >Delete</button
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Paginator -->
                <Pagination
                    :links="non_recurring.links"
                    :total="non_recurring.total"
                    :from="non_recurring.from"
                    :to="non_recurring.to"
                />
                <ConfirmationModel
                    :showModal="showDeleteModal"
                    @confirm="handleDelete"
                    @cancel="cancelDelete"
                />
            </div>
        </main>
    </div>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import Layout from "../Shared/Layout";
import Pagination from "../../../Shared/Pagination.vue";
import { Inertia } from "@inertiajs/inertia";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";
import { ref, watch } from "vue";
import ConfirmationModel from "../../../Shared/ConfirmationModel.vue";
import SearchFilter from "../../../Shared/SearchFilter";

export default {
    setup(props) {
        let searchOperationalCost = ref(props.filters.search);

        watch(
            searchOperationalCost,
            throttle((value) => {
                Inertia.get(
                    "/operational-cost",
                    { search: value },
                    {
                        preserveState: true,
                        replace: true,
                    },
                );
            }, 500),
        );

        return { searchOperationalCost };
    },
    data() {
        return {
            form: {
                search: this.filters.search,
            },
            page_title: "Operational Cost",
            page_subtitle: "Manage your Operational Cost",
            breadcrumbs: [
                { link: "/lawyer/dashboard", label: "Lawyer" },
                { link: "/lawyer/operational-cost", label: "Operational Cost" },
                // ...this.bank_accounts.map((account) => ({
                //     link: `/lawyer/firm-accounts/${account.id}/detail`,
                //     label: account.label,
                // })),
            ],
            showDeleteModal: false,
            selectedAcc: null,
        };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(
                    `/lawyer/operational-cost`,
                    pickBy(this.form),
                    {
                        preserveState: true,
                    },
                );
            }, 150),
        },
    },
    props: {
        recurring: Object,
        non_recurring: Object,
        filters: Object,
    },
    components: { Head, Pagination, ref, ConfirmationModel, SearchFilter },
    layout: Layout,
    methods: {
        deleteAcc(acc) {
            if (confirm("Are you sure you want to delete this cost?")) {
                Inertia.delete(`/lawyer/operational-cost/${acc.id}`);
            }
        },
        formatToTwoDecimal(num) {
            if (num == null) {
                return "0.00";
            } else {
                return num.toFixed(2); // Formats the number to 2 decimal places
            }
        },
        formatString(str) {
            return str
                .split("_") // Split by underscores
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1)) // Capitalize the first letter of each word
                .join(" "); // Join the words with spaces
        },
        showDeleteConfirmation(acc) {
            this.selectedAcc = acc;
            this.showDeleteModal = true;
        },
        handleDelete() {
            if (this.selectedAcc) {
                Inertia.delete(
                    `/lawyer/operational-cost/${this.selectedAcc.id}`,
                );
                this.showDeleteModal = false;
            }
        },
        cancelDelete() {
            this.showDeleteModal = false;
        },
        reset() {
            this.form = mapValues(this.form, () => null);
        },
    },
};
</script>
