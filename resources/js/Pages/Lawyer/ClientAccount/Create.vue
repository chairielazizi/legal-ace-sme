<template>
    <Head title="Add new transaction" />

    <page-heading :page_title="page_title" :breadcrumbs="breadcrumbs" />

    <div
        class="max-w-3xl bg-white rounded-md border border-gray-300 overflow-hidden"
    >
        <form @submit.prevent="store">
            <div class="p-8 space-y-12">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">
                        Transaction Detail
                    </h2>
                    <!-- <p class="mt-1 text-sm leading-6 text-gray-600">The personal information of the employee.</p> -->

                    <div
                        class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 md:grid-cols-2"
                    >
                        <date-input
                            v-model="form.date"
                            :error="form.errors.date"
                            label="Date"
                            required
                        />
                        <!-- <text-input
                            v-model="form.description"
                            type="description"
                            :error="form.errors.description"
                            label="Description"
                            required
                        /> -->
                        <select-input
                            v-model="form.transaction_type"
                            :error="form.errors.transaction_type"
                            label="Transaction Type"
                            required
                        >
                            <option disabled value="">
                                Please select transaction type
                            </option>
                            <option value="funds in">Funds In</option>
                            <option value="funds out">Funds Out</option>
                        </select-input>
                        <div v-if="form.transaction_type == 'funds in'">
                            <select-input
                                v-model="form.description"
                                :error="form.errors.description"
                                label="Description"
                                required
                            >
                                <option disabled value="">
                                    Select Description
                                </option>
                                <option value="payment_received">
                                    Deposit
                                </option>
                            </select-input>
                        </div>
                        <div v-if="form.transaction_type == 'funds out'">
                            <select-input
                                v-model="form.description"
                                :error="form.errors.description"
                                label="Description"
                                required
                            >
                                <option disabled value="">
                                    Select Description
                                </option>
                                <option value="payment_received">
                                    Payment
                                </option>
                            </select-input>
                        </div>
                        <text-input
                            v-model="form.document_number"
                            :error="form.errors.document_number"
                            label="Document number"
                            required
                        />
                        <file-input
                            v-model="form.upload"
                            :errors="form.errors.upload"
                            class="pb-8 pr-6 w-full lg:w-1/2"
                            label="Upload Document"
                            accept=".jpg,.png,.pdf,.doc,.docx"
                        />
                        <text-input
                            v-model="form.amount"
                            :error="form.errors.amount"
                            :type="'number'"
                            label="Amount"
                            step="0.01"
                            min="0"
                            required
                        />
                        <select-input
                            v-model="form.payment_method"
                            :error="form.errors.payment_method"
                            label="Payment Method"
                            required
                        >
                            <option disabled value="">
                                Please payment method
                            </option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="credit_card">Credit Card</option>
                        </select-input>
                        <text-input
                            v-model="form.reference"
                            :error="form.errors.reference"
                            label="Reference"
                            required
                        />
                        <!-- <div v-if="errors">
                            <ul>
                                <li v-for="(error, key) in errors" :key="key">
                                    {{ error }}
                                </li>
                            </ul>
                        </div> -->
                    </div>
                </div>
            </div>

            <div
                class="flex flex-row-reverse space-x-2 space-x-reverse items-center justify-start px-8 py-4 bg-gray-50 border-t border-gray-100"
            >
                <loading-button
                    :loading="form.processing"
                    :disabled="!form.isDirty"
                    class="btn-primary"
                    type="submit"
                    >Submit</loading-button
                >
                <Link
                    v-on:click="goBack()"
                    as="button"
                    class="btn-cancel"
                    :disabled="form.processing"
                >
                    Cancel
                </Link>
            </div>
        </form>
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
        acc_number: Object,
        bank_accounts: Object,
        errors: Object,
    },
    data() {
        return {
            page_title: "Add new transaction",
            breadcrumbs: [
                { link: "/lawyer/dashboard", label: "Lawyer" },
                { link: "/lawyer/client-accounts", label: "Client Account" },
                ...this.bank_accounts.map((account) => ({
                    link: `/lawyer/client-accounts/${account.id}/detail`,
                    label: account.label,
                })),
                { link: null, label: "Create" },
            ],
            form: this.$inertia.form({
                date: "",
                bank_account_id: this.acc_number,
                description: "",
                transaction_type: "",
                document_number: "",
                upload: null,
                amount: "",
                payment_method: "",
                reference: "",
            }),
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
    },
};
</script>
