<template>
    <div class="container-fluid players">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" id="vtable">
                        <el-col :span="4">
                            <el-input v-model="filters.value" placeholder="Username"></el-input>
                        </el-col>
                        <data-tables-server 
                            :data="data" 
                            :total="total" 
                            :loading="loading" 
                            :table-props="tableProps"
                            :filters="filters"
                            @query-change="loadData"
                            @current-page-change="handleCurrentPageChange"
                            @current-change="handleCurrentChange"
                            @prev-click="handlePrevClick"
                            @size-change="handleSizeChange"
                            @selection-change="handleSelectionChange"
                            @row-click="handleRowClick"
                            :pagination-props="{ pageSizes: [10, 25, 50] }">
                            <el-table-column v-for="title in titles" :prop="title.prop" :label="title.label" :key="title.label" sortable="custom">
                            </el-table-column>
                        </data-tables-server>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        components: {

        },
        data: function() { 
            return {
                data: null,
                titles: [
                    // { prop: 'rank_pos', label: '#'},
                    { prop: 'nickname', label: 'Nickname'},
                    { prop: 's2_tickets', label: 'Step2 Tickets'},
                    {prop: 's3_tickets', label: 'Step3 Tickets'},
                    {prop: 's4_tickets', label: 'Step4 Tickets'},
                ],
                // filters: [{
                //     prop: ['Username'],
                //     value: ''
                // }],
                filters: {
                    props: 'nickname',
                    value: '',
                    def: [{
                        'value': '',
                    }]
                },
                customButtonsForRow(row) {
                    return [
                    ]
                },
                loading: false,
                total: 0,
                tableProps: {
                    border: true,
                    stripe: true,
                    defaultSort: {
                        prop: 'nickname',
                        order: 'ascending'
                    }
                },
                layout: 'table, pagination',
                queryInfo: false,
            }
        },
        mounted() {
        },
        methods: {
            async loadData(queryInfo) {
                this.loading = true
                const res = await axios.post('/players', queryInfo);
                let { data, total } = {data: res.data.data, total: res.data.total}
                this.data = data
                this.total = total
                this.loading = false
                this.queryInfo = queryInfo
            },
            handleCurrentPageChange(page) {},
            handleCurrentChange(currentRow) {},
            handlePrevClick(page) {},
            handleSizeChange(size) {},
            handleSelectionChange(val) {},
            handleRowClick(row){
                console.log("handleRowClick", row.nickname)
                window.location.href = window.location.origin + '/player/' + encodeURIComponent(row.nickname)
            },
        }
    }
</script>
<style scoped>
    .card{
        background-color: inherit;
    }

    .row .pagination{
        display: flex;
    }
    .formular{
        margin-left: 0.9em;
    }
    .page-link {
        height: calc(1.4em + 0.75rem + 2px);
        position: relative;
        display: block;
        padding: .5rem 1.5rem;
        margin-left: -1px;
        line-height: calc(1.4em + 0.75rem + 2px);
        vertical-align: middle;
    }
</style>
<style>
    .el-table th, .el-table tr {
        background-color: inherit!important;
    }
    .el-table, .el-table__expanded-cell {
        background-color: inherit!important;
    }

    .el-table--striped .el-table__body tr.el-table__row--striped td {
        background: inherit!important;
    }

    .el-pagination{
        margin-top: 15px;
    }

    .el-pagination button, .el-pagination button:disabled, .el-dialog, .el-pager li, .el-pagination .btn-next, .el-pagination .btn-prev {
        background: inherit;
        color: #888888!important;
    }

    .el-dialog, .el-pager li{
        color: #888888!important;
    }

    .el-table * {
        color: #888888!important;
    }

    .sc-table, .el-table, .el-table tr td, .el-table tr th, .el-table table{
        border: none;
    }

    .el-table tr td, .el-table tr th{
        cursor: pointer;
    }

    .el-table tbody tr.el-table__row:hover, .el-table tbody tr.el-table__row--striped:hover{
        background-color: #eeeeee!important;
    }

    .el-table thead tr th{
        border-bottom: 1px solid #555555;
    }

    .el-table--border::after, .el-table--group::after, .el-table::before {
        background-color: transparent;
    }
</style>