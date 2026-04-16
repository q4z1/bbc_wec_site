<template>
  <div class="registration">
    <el-alert v-if="alert" :title="alertMsg" :type="alertTypeEl" show-icon closable @close="alert = false" style="margin-bottom:0.75rem;" />
    <h3>Registration</h3>
    <div style="display:flex;justify-content:center;">
      <div style="flex:1;">
        <FullCalendar v-if="game_dates" ref="fullCalendar" :options="calendarOptions" />
      </div>
    </div>
    <!-- Game Date Modal -->
    <el-dialog append-to-body v-model="showDateDialog" :title="date ? 'Step ' + date.step + ' - ' + date.date : ''" :width="gModalSize === 'xl' ? '90%' : '500px'" destroy-on-close>
      <game-date-component v-if="date" @show-alert="showAlert" @update-dates="updateDates"
        @close-dialog="showDateDialog = false" :date="date" :utcDate="utcDate" :fp="fp" />
    </el-dialog>
    <!-- New Game Date Modal -->
    <el-dialog append-to-body v-if="arole === 'a' || arole === 's'" v-model="showNewDateDialog" title="New Game" width="500px" destroy-on-close>
      <game-date-new-component @show-alert="showAlert" @update-dates="updateDates"
        @close-dialog="showNewDateDialog = false" />
    </el-dialog>
  </div>
</template>
<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import GameDateComponent from './GameDateComponent.vue';
import GameDateNewComponent from './GameDateNewComponent.vue';
import FingerprintJS from '@fingerprintjs/fingerprintjs';
export default {
  props: ['gamedates', 'calstart'],
  components: { FullCalendar, GameDateComponent, GameDateNewComponent },
  data() {
    return {
      game_dates: null,
      date: null,
      utcDate: null,
      fp: null,
      gModalSize: 'md',
      alertVar: 'danger',
      alertMsg: '',
      alert: false,
      arole: window.arole === 's' ? 'a' : window.arole,
      showDateDialog: false,
      showNewDateDialog: false,
    };
  },
  computed: {
    alertTypeEl() {
      const map = { danger: 'error', success: 'success', warning: 'warning', info: 'info' };
      return map[this.alertVar] || 'error';
    },
    calendarOptions() {
      return {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        customButtons: {
          addEvent: {
            text: '+',
            click: () => { this.showNewDateDialog = true; },
          },
        },
        firstDay: 1,
        validRange: { start: this.calstart },
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: window.arole === 'a' || window.arole === 's' ? 'addEvent' : '',
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false, hour12: false },
        now: new Date(Date.now() - 90 * 60 * 1000),
        eventOrder: (a, b) => (a.extendedProps.sortOrder ?? 0) - (b.extendedProps.sortOrder ?? 0),
        events: this.game_dates,
        eventDidMount: function (info) {
          const colorMap = { 0: 'info', 1: 'primary', 2: 'success', 3: 'warning', 4: 'danger' };
          const step = info.event.extendedProps.step;
          const colorVar = 'var(--' + (colorMap[step] ?? 'primary') + ')';
          const timeEl = info.el.querySelector('.fc-event-time');
          const titleEl = info.el.querySelector('.fc-event-title');
          if (timeEl) timeEl.style.color = colorVar;
          if (titleEl) titleEl.style.color = colorVar;
        },
        eventClick: (info) => { this.showDate(info.event.id); },
      };
    },
  },
  mounted() {
    if (window.arole === 'a' || window.arole === 's') this.gModalSize = 'xl';
    this.game_dates = this.formatDates(this.gamedates);
    const fpPromise = FingerprintJS.load();
    (async () => {
      const fp = await fpPromise;
      const result = await fp.get();
      this.fp = result.visitorId;
    })();
  },
  methods: {
    showAlert(msg, variant, duration = 5) {
      this.alertVar = variant;
      this.alertMsg = msg;
      this.alert = duration;
    },
    showDate(id) {
      axios.get('/registration/date/get/' + id).then(
        (response) => {
          if (response.data.success === true) {
            this.date = response.data.date;
            this.utcDate = response.data.utcDate;
            this.showDateDialog = true;
          } else { console.log(response.data); }
        },
        (error) => { console.log(error); }
      );
    },
    updateDates(dates) {
      this.game_dates = false;
      this.$nextTick(() => { this.game_dates = this.formatDates(dates); });
    },
    formatDates(dates) {
      let new_dates = [];
      for (let i in dates) {
        let date = dates[i];
        let num = date.num;
        let hour = parseInt(date.date.substr(11, 2));
        let minute = parseInt(date.date.substr(14, 2));
        let calDate = date.date;
        if (hour < 14) {
          let d = new Date(date.date.replace(' ', 'T'));
          d.setDate(d.getDate() - 1);
          calDate = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') + ' ' + date.date.substr(11);
        }
        let sortOrder = hour < 14 ? (hour + 24) * 60 + minute : hour * 60 + minute;
        const colors = { 0: 'info', 1: 'primary', 2: 'success', 3: 'warning', 4: 'danger' };
        new_dates.push({
          id: date.id,
          title: date.step > 0 ? 'Step' + date.step : date.title,
          start: calDate.replace(' ', 'T'),
          end: calDate.replace(' ', 'T').replace(calDate.substr(calDate.length - 2, 2), '59'),
          description: 'Players: ' + num + '/10',
          color: 'var(--' + colors[date.step] + ')',
          sortOrder,
          step: date.step,
        });
      }
      return new_dates;
    },
  },
};
</script>
<style lang="scss">
.registration {
  .fc {
    .fc-header-toolbar {
      .fc-addEvent-button { font-size: 1.2em; padding: 0.2em 0.65em; }
      .fc-icon { font-size: 1em; }
    }
    .fc-view-harness {
      height: 720px !important;
      .fc-event { cursor: pointer; }
      .fc-daygrid-body {
        height: auto !important;
        table {
          height: auto !important;
          tbody tr { height: 115px !important; td { overflow-y: auto; } }
        }
      }
    }
  }
}
</style>
