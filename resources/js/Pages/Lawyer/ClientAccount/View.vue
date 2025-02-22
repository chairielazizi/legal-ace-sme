<template>
    <Head title="Transaction" />

    <page-heading :page_title="page_title" :breadcrumbs="breadcrumbs" />

    <div
        class="max-w-3xl bg-white rounded-md border border-gray-300 overflow-hidden"
    >
        <dl>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">Date</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ clientAccounts.date }}
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ formatString(clientAccounts.description) }}
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">
                    Transaction Type
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ formatString(clientAccounts.transaction_type) }}
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">
                    Document Number
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ clientAccounts.document_number }}
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">Document</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <Link
                        v-if="
                            clientAccounts.upload != null &&
                            clientAccounts.upload != ''
                        "
                        class="text-blue-700 btn btn-blue-500 hover:btn-blue-700 transition duration-300 ease-in-out hover:shadow-md hover:shadow-blue-500/50"
                        :href="`/lawyer/client-account/download/${clientAccounts.id}`"
                        @click.prevent="downloadFile(clientAccounts.id)"
                        >Download Document</Link
                    >
                    <p v-else>No Document was uploaded</p>
                    <!-- {{ clientAccounts.upload }} -->
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">Funds In</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    RM {{ formatToTwoDecimal(clientAccounts.debit) }}
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">Funds Out</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    RM {{ formatToTwoDecimal(clientAccounts.credit) }}
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">
                    Payment Method
                </dt>
                <dd
                    class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-wrap"
                >
                    {{ formatString(clientAccounts.payment_method) }}
                </dd>
            </div>
            <div
                class="bg-white border-b px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
            >
                <dt class="text-sm font-medium text-gray-500">Reference</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ clientAccounts.reference }}
                </dd>
            </div>
        </dl>

        <div
            class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100"
        >
            <!-- <Link as="button" :href="`/admin/bank-accounts/${bank_account.id}/edit`" class="btn-primary">
                Edit
            </Link>  -->
            <button @click="goBack">Back</button>
        </div>
    </div>
</template>

<script>
import Layout from "../Shared/Layout";
import TextInput from "../../../Shared/TextInput";
import SelectInput from "../../../Shared/SelectInput";
import DateInput from "../../../Shared/DateInput";
import LoadingButton from "../../../Shared/LoadingButton";
import FileInput from "../../../Shared/FileInput";
import { Switch } from "@headlessui/vue";
import { Head } from "@inertiajs/inertia-vue3";
import { useForm } from "@inertiajs/inertia-vue3";
import axios from "axios";

export default {
    components: {
        TextInput,
        SelectInput,
        DateInput,
        LoadingButton,
        FileInput,
        Switch,
        Head,
    },
    layout: Layout,
    props: {
        clientAccounts: Object,
        acc_id: Object,
        bank_accounts: Object,
    },
    data() {
        return {
            page_title: "Transaction",
            breadcrumbs: [
                { link: "/lawyer/dashboard", label: "Lawyer" },
                { link: "/lawyer/client-accounts", label: "Client Account" },
                ...this.bank_accounts.map((account) => ({
                    link: `/lawyer/client-accounts/${account.id}/detail`,
                    label: account.label,
                })),
                { link: null, label: "View" },
            ],
        };
    },
    methods: {
        store() {
            if (this.form.isDirty) {
                this.form.post("/lawyer/client-accounts");
            } else {
                alert("You need to fill in the form first.");
            }
        },
        goBack() {
            // window.history.go(-1);
            if (this.bank_accounts.length > 0) {
                const accountId = this.bank_accounts[0].id;
                this.$inertia.visit(
                    `/lawyer/client-accounts/${accountId}/detail`,
                );
            } else {
                console.error("No bank accounts available.");
            }
        },
        formatString(str) {
            return str
                .split("_") // Split by underscores
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1)) // Capitalize the first letter of each word
                .join(" "); // Join the words with spaces
        },
        formatToTwoDecimal(num) {
            if (num == null) {
                return "0.00";
            } else {
                return num.toFixed(2); // Formats the number to 2 decimal places
            }
        },
        downloadFileNative(id) {
            // window.open(`/lawyer/firm-account/download/${id}`, "_blank");
            window.location.href = `/lawyer/client-account/download/${id}`;
        },
        downloadFile(id) {
            axios
                .get(`/lawyer/client-account/download/${id}`, {
                    responseType: "blob",
                })
                .then((response) => {
                    const file = new Blob([response.data], {
                        type: response.headers["content-type"],
                    });
                    const fileUrl = URL.createObjectURL(file);
                    const a = document.createElement("a");
                    a.href = fileUrl;
                    a.download = response.headers["content-disposition"]
                        .split("filename=")[1]
                        .trim('"');
                    a.click();
                    URL.revokeObjectURL(fileUrl);
                })
                .catch((error) => {
                    console.error(`Error downloading file: ${error.message}`);
                    // Prevent the redirect
                    window.history.go(-1);
                    alert(`Error downloading file: ${error.message}`);
                });
        },
    },
};
</script>
