<template>
    <div class="shoutbox">
        <h3>Shoutbox</h3>
        <div v-if="alert" style="margin-bottom:0.75rem;">
            <el-alert :title="alertMsg" :type="alertTypeEl" show-icon closable @close="alert = false" />
        </div>
        <div class="box">
            <div v-if="posts" class="card" ref="scrollBox" :style="{ height: cardHeight + 'px', overflowY: 'auto' }">
                <InfiniteLoading v-if="initialLoaded" direction="top" @infinite="infiniteHandler">
                    <template #no-more><span></span></template>
                    <template #no-results><span></span></template>
                    <template #spinner><span></span></template>
                </InfiniteLoading>
                <ul class="sb-list" v-if="show">
                    <li v-for="(post, index) in posts" :key="post.id" class="sb-post-item"
                        :class="{ 'admin-post': post.active === 3, 'warning-post': post.active === 4 }">
                        <div class="sb-post-head">
                            <div class="sb-post-meta">
                                <span style="margin-right:0.5rem;font-weight:600;">#{{ post.id }}</span>
                                <span :style="'font-weight:600;color:' + (post.active > 1 ? '#f56c6c' : '#409eff')">{{ post.nickname }}</span>
                                <small style="margin-left:0.5rem;">{{ date(index) }}</small>
                            </div>
                            <div class="sb-post-info" v-if="arole === 's'">
                                <div class="sb-post-info-row"><small style="color:#67c23a;font-weight:600;">IP:&nbsp;</small><small>{{ post.ip }}</small></div>
                                <div class="sb-post-info-row"><small style="color:#409eff;font-weight:600;">Fingerprint:&nbsp;</small><small>{{ post.fp }}</small></div>
                            </div>
                            <div class="sb-post-info" v-if="arole === 'a'">
                                <div class="sb-post-info-row"><small style="color:#409eff;font-weight:600;">Fingerprint:&nbsp;</small><small>{{ post.fp }}</small></div>
                            </div>
                            <div class="sb-post-actions">
                                <el-icon v-if="user !== null && post.user_id === user.id" style="color:#e6a817;margin-right:0.5rem;cursor:pointer" @click="editPost(post.id)"><EditPen /></el-icon>
                                <el-icon v-if="arole === 'a' || arole === 's'" style="color:#f56c6c;cursor:pointer" @click="del(post.id)"><Delete /></el-icon>
                            </div>
                        </div>
                        <hr />
                        <div v-html="post.message"></div>
                        <div v-if="post.created_at < post.updated_at">
                            <small style="font-style:italic;">Last edited: {{ date(index, true) }}</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sb-form-wrap sb-input-area">
            <div class="sb-form-inner">
                <div class="sb-form-left">
                    <el-input placeholder="PokerTH Nickname" v-model="nickname" :disabled="arole !== ''" />
                    <div v-if="arole === 'a' || arole === 's'" class="sb-toggles">
                        <div class="sb-toggle-row">
                            <span>Admin-post:</span>
                            <el-switch v-model="admin_post" />
                        </div>
                        <div class="sb-toggle-row">
                            <span>Global Notice:</span>
                            <el-switch v-model="global_warning" />
                        </div>
                    </div>
                </div>
                <div class="sb-form-right">
                    <div style="flex:1;">
                        <el-input type="textarea" :rows="3" placeholder="Type your message" v-model="sbmsg" />
                    </div>
                    <div class="sb-send">
                        <el-button type="success" @click="postMsg"><el-icon><Promotion /></el-icon></el-button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Post Dialog -->
        <el-dialog append-to-body v-model="showDeleteDialog" :title="'Delete Post #' + sbid">
            Are you sure to delete post <strong style="color:#e6a817;">#{{ sbid }}</strong>?<br />
            <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
            <template #footer>
                <el-button @click="showDeleteDialog = false">Cancel</el-button>
                <el-button type="danger" @click="doDel">Yes, Delete!</el-button>
            </template>
        </el-dialog>

        <!-- Edit Post Dialog -->
        <el-dialog append-to-body v-model="showEditDialog" :title="'Edit Post #' + sbid">
            <el-input type="textarea" :rows="3" v-model="sbmsg" />
            <div style="margin-top:0.5rem;display:flex;align-items:center;justify-content:space-between;" v-if="arole !== ''">
                <span>Admin-post:</span><el-switch v-model="admin_post" />
            </div>
            <div style="margin-top:0.5rem;display:flex;align-items:center;justify-content:space-between;" v-if="arole === 'a' || arole === 's'">
                <span>Global Notice:</span><el-switch v-model="global_warning" />
            </div>
            <template #footer>
                <el-button @click="showEditDialog = false">Cancel</el-button>
                <el-button type="danger" @click="doUpdate">Update Post!</el-button>
            </template>
        </el-dialog>
    </div>
</template>
<script>
import FingerprintJS from '@fingerprintjs/fingerprintjs';
import InfiniteLoading from 'v3-infinite-loading';
export default {
    components: { InfiniteLoading },
    props: ['user'],
    data() {
        return {
            reason: '',
            fp: null,
            arole: window.arole,
            total: 0,
            posts: [],
            sbmsg: null,
            sbid: null,
            nickname: null,
            offset: 0,
            admin_post: false,
            global_warning: false,
            show: true,
            initialLoaded: false,
            alertVar: 'danger',
            alertMsg: '',
            alert: false,
            showDeleteDialog: false,
            showEditDialog: false,
            cardHeight: 400,
        };
    },
    watch: {
        admin_post(val) { if (val) this.global_warning = false; },
        global_warning(val) { if (val) this.admin_post = false; },
    },
    computed: {
        alertTypeEl() {
            const map = { danger: 'error', success: 'success', warning: 'warning', info: 'info' };
            return map[this.alertVar] || 'error';
        },
    },
    mounted() {
        const fpPromise = FingerprintJS.load();
        (async () => {
            const fp = await fpPromise;
            const result = await fp.get();
            this.fp = result.visitorId;
        })();
        if (this.user !== null) this.nickname = this.user.name;
        this.$nextTick(() => this.updateCardHeight());
        this._resizeObserver = new ResizeObserver(() => this.updateCardHeight());
        this._resizeObserver.observe(document.body);
        // Initiales Laden: neueste Posts holen und danach nach unten scrollen
        this.filter(null, false);
        setInterval(() => { this.filter(null, true); }, 10000);
    },
    beforeUnmount() {
        if (this._resizeObserver) this._resizeObserver.disconnect();
    },
    methods: {
        updateCardHeight() {
            const el = this.$el;
            if (!el) return;
            const card = this.$refs.scrollBox;
            if (!card) return;
            const cardTop = card.getBoundingClientRect().top;
            const footer = document.querySelector('footer.page-footer');
            const footerH = footer ? footer.offsetHeight : 0;
            const inputArea = el.querySelector('.sb-input-area');
            const inputH = inputArea ? inputArea.offsetHeight : 0;
            const mainEl = document.querySelector('main');
            const mainPadBottom = mainEl ? parseInt(window.getComputedStyle(mainEl).paddingBottom) : 24;
            const available = window.innerHeight - cardTop - inputH - footerH - mainPadBottom - 8;
            this.cardHeight = Math.max(200, available);
        },
        date(index, updated = false) {
            const d = new Date(Date.parse(updated ? this.posts[index].updated_at : this.posts[index].created_at));
            return d.getFullYear() + '-' + this.pad(d.getMonth() + 1) + '-' + this.pad(d.getDate()) + ' ' + this.pad(d.getHours()) + ':' + this.pad(d.getMinutes()) + ':' + this.pad(d.getSeconds());
        },
        pad(num) { return String(num).padStart(2, '0'); },
        filter(state = null, update = false) {
            const data = new FormData();
            if (this.sbid !== null) data.append('id', this.sbid);
            data.append('offset', update ? 0 : this.offset);
            axios({ method: 'post', url: '/shoutbox', data, headers: { 'Content-Type': 'application/json' } })
                .then(response => {
                    if (response.data.success === true) {
                        if (state === null) {
                            const posts = response.data.posts;
                            let scroll = true;
                            if (!update) {
                                // Server liefert DESC (neueste zuerst) → umkehren zu ASC damit neueste unten landen
                                this.posts = [...posts].reverse();
                                // Offset setzen damit InfiniteLoader ältere Posts lädt
                                this.offset = posts.length;
                            } else {
                                scroll = false;
                                for (const i in posts) {
                                    if (posts[i].id > this.posts[this.posts.length - 1].id) {
                                        this.posts.push(posts[i]);
                                        scroll = true;
                                    }
                                }
                            }
                            if (scroll) {
                                this.$nextTick(() => {
                                    if (!update) this.updateCardHeight();
                                    const box = this.$refs.scrollBox;
                                    if (box) box.scrollTop = box.scrollHeight;
                                    // InfiniteLoader erst einblenden nachdem Scroll ganz unten ist
                                    if (!update) this.initialLoaded = true;
                                });
                            }
                        } else {
                            if (response.data.posts.length !== 0) {
                                this.offset += 200;
                                this.posts.unshift(...response.data.posts.reverse());
                                state.loaded();
                            } else {
                                state.complete();
                            }
                        }
                    }
                }, (error) => { console.log(error); });
        },
        postMsg() {
            if (!this.nickname) { this.alertMsg = 'A nickname is mandatory!'; this.alertVar = 'danger'; this.alert = true; return; }
            if (!this.sbmsg) { this.alertMsg = 'A message is mandatory!'; this.alertVar = 'danger'; this.alert = true; return; }
            const data = new FormData();
            data.append('message', this.sbmsg);
            data.append('fp', this.fp);
            data.append('nickname', this.nickname);
            data.append('admin_post', this.admin_post ? 1 : 0);
            data.append('global_warning', this.global_warning ? 1 : 0);
            axios({ method: 'post', url: '/shoutbox/new', data, headers: { 'Content-Type': 'application/json' } })
                .then(response => {
                    if (response.data.success === true) {
                        this.sbmsg = null;
                        // Neuen Post direkt einfügen statt page-reload
                        this.filter(null, true);
                    }
                }, (error) => { console.log(error); });
        },
        editPost(id) {
            this.sbid = id;
            axios.get('/shoutbox/map/' + id).then(response => {
                if (response.data.success === true) {
                    const post = response.data.post;
                    this.sbmsg = post.message;
                    this.admin_post = post.active === 3;
                    this.global_warning = post.active === 4;
                    this.showEditDialog = true;
                }
            }, (error) => { console.log(error); });
        },
        doUpdate() {
            const data = new FormData();
            data.append('message', this.sbmsg);
            data.append('admin_post', this.admin_post ? 1 : 0);
            data.append('global_warning', this.global_warning ? 1 : 0);
            axios({ method: 'post', url: '/shoutbox/update/' + this.sbid, data, headers: { 'Content-Type': 'application/json' } })
                .then(response => {
                    if (response.data.success === true) {
                        const post = response.data.post;
                        for (const i in this.posts) {
                            if (this.posts[i].id == post.id) { this.posts[i] = post; break; }
                        }
                        this.showEditDialog = false;
                        this.sbid = null;
                    }
                }, (error) => { console.log(error); });
            this.sbmsg = null;
        },
        del(id) { this.sbid = id; this.showDeleteDialog = true; },
        doDel() {
            if (!this.reason) { this.alertMsg = 'Please enter a reason!'; this.alertVar = 'danger'; this.alert = true; return; }
            axios.post('/shoutbox/delete/' + this.sbid, { reason: this.reason })
                .then(response => {
                    if (response.data.success === true) {
                        this.alertMsg = 'Shoutbox Message deleted!'; this.alertVar = 'success'; this.alert = true;
                        this.posts = this.posts.filter(p => p.id != this.sbid);
                        this.showDeleteDialog = false;
                    }
                }, (error) => { console.log(error); });
        },
        infiniteHandler($state) { this.filter($state); },
    },
};
</script>
<style lang="scss" scoped>
.shoutbox {
    .box {
        .card {
            overflow-y: auto;
        }
    }
    .sb-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sb-post-item {
        padding: 0.5rem 1rem;
    }
    .sb-post-head {
        display: flex;
        gap: 0.5rem;
        align-items: flex-start;
    }
    .sb-post-meta {
        flex: 1;
        min-width: 0;
    }
    .sb-post-info {
        flex: 1;
        min-width: 0;
    }
    .sb-post-info-row {
        display: flex;
    }
    .sb-post-actions {
        flex-shrink: 0;
        text-align: right;
    }
    .sb-form-wrap {
        margin-top: 0.5rem;
    }
    .sb-form-inner {
        display: flex;
        gap: 0.75rem;
        border: 1px solid var(--el-border-color);
        border-radius: 4px;
        padding: 0.75rem;
        background: var(--el-bg-color);
    }
    .sb-form-left {
        flex: 0 0 auto;
        min-width: 160px;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .sb-toggles {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .sb-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sb-form-right {
        flex: 1;
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }
    .sb-send {
        flex-shrink: 0;
        display: flex;
        align-items: flex-end;
    }
    hr {
        margin: 0.5rem 0;
    }
    li.admin-post { background-color: rgba(240, 173, 78, 0.15); }
    li.warning-post { background-color: rgba(217, 83, 79, 0.15); }
    .el-icon { font-size: 1.1em; }
}
</style>
