<template>
    <div class="container-fluid">
        <div class="reports">
            <h4 class="mb-1">Reports</h4>
            <div class="row">
                <div class="col-12">
                    <select class="custom-select float-right w-auto"
                            v-model="selected" name="orderSelect">
                        <option :key="index" v-for="(option,index) in options" :value="index">
                            {{ option }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card border-shadow-top w-100 px-0 mw-100">
                        <div class="card-body border-bottom py-1 bg-transparent container-fluid">
                            <div class="row">
                                <div class="col-5 px-3">
                                    <h6 class="card-title mb-2">Name</h6>
                                </div>
                                <div class="col-7 px-3">
                                    <h6 class="card-title mb-2">Description</h6>
                                </div>
                            </div>
                        </div>
                        <div :class="[{'card-body': true},
                                      {'border-shadow': true},
                                      {'py-3': true},
                                      {'reports-list': true},
                                      {'active': selectedReport===report.slug}]"
                             @click="loadReport(report, $event)"
                             :key="report.slug" v-for="report in reports">
                            <div class="row">
                                <div class="col-5 px-3">
                                    <a :href="reportUrl(report.slug)">
                                        {{ report.name }}
                                    </a>
                                </div>
                                <div class="col-7 px-3">
                                    <strong>{{ report.description }}</strong><br>
                                    {{ report.inDepthDescription }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import config from '@/config';
import { useStore } from 'vuex';
import {fetchReports} from '@/services/reports-service';
import ActivityGroomersServiceAreaComponent from '@/components/reports/ActivityGroomersServiceAreaComponent';
import ActivityComponent from '@/components/reports/ActivityComponent';

export default {
    name: 'Reports',
    components: {
        ActivityGroomersServiceAreaComponent,
        ActivityComponent,
    },
    data() {
        return {
            reports: [],
            selected: 'Sort Reports',
            options: {
                0: '-- Sort Reports --',
                1: 'Title: A to Z',
                2: 'Title: Z to A',
            },
            selectedReport: 'active-groomers-by-service-area22',
        };
    },
    methods: {
        reportUrl(slug) {
            return window.admin_url + slug;
        },
        loadReport(report, e) {
            window.location = this.reportUrl(report.slug)
        },
        reportSort(value) {
            console.log(value)
            if (value === '1') {
              this.reports.sort((a, b) => (a.name.localeCompare(b.name)));
            } else if (value === '2') {
              this.reports.sort((a, b) => (b.name.localeCompare(a.name)));
            }
        }
    },
    created() {
        this.reports = fetchReports();
    },
    watch: {
        selected(newValue) {
            this.reportSort(newValue)
        },
    },
};
</script>
