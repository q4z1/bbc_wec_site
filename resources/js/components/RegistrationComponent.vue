<template>
  <div class="registration">
    <b-alert
      v-if="alert"
      :show="alert"
      dismissible
      :variant="alertVar"
      @dismissed="alert = false"
    >
      {{ alertMsg }}
    </b-alert>
    <h3>Registration</h3>
    <b-row class="d-flex justify-content-center">
      <b-col class="calendar"
        ><FullCalendar
          v-if="game_dates"
          ref="fullCalendar"
          :options="calendarOptions"
      /></b-col>
    </b-row>
    <b-modal
      :size="gModalSize"
      id="modal-date"
      :title="date ? 'Step ' + date.step + ' - ' + date.date : ''"
      hide-footer
    >
      <game-date-component
        @show-alert="showAlert"
        @update-dates="updateDates"
        v-if="date"
        :date="date"
        :utcDate="utcDate"
        :fp="fp"
      />
    </b-modal>
    <b-modal
      id="modal-newdate"
      title="New Game"
      v-if="arole === 'a' || arole === 's'"
      hide-footer
    >
      <game-date-new-component
        @show-alert="showAlert"
        @update-dates="updateDates"
      />
    </b-modal>
  </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import GameDateComponent from "./GameDateComponent.vue";
import FingerprintJS from "@fingerprintjs/fingerprintjs";
export default {
  props: ["gamedates", "calstart"],
  components: {
    FullCalendar,
    GameDateComponent,
  },
  data() {
    return {
      game_dates: null,
      date: null,
      utcDate: null,
      fp: null,
      gModalSize: "md",
      alertVar: "danger",
      alertMsg: "",
      alert: false,
      arole: window.arole === "s" ? "a" : window.arole,
    };
  },
  computed: {
    calendarOptions: function () {
      return {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        customButtons: {
          addEvent: {
            text: "+",
            click: () => {
              this.$bvModal.show("modal-newdate");
            },
          },
        },
        firstDay: 1,
        validRange: {
          start: this.calstart,
        },
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: window.arole === "a" || window.arole === "s" ? "addEvent" : "",
        },
        eventTimeFormat: {
          hour: "2-digit",
          minute: "2-digit",
          meridiem: false,
          hour12: false,
        },
        eventOrder: (a, b) => (a.extendedProps.sortOrder ?? 0) - (b.extendedProps.sortOrder ?? 0),
        events: this.game_dates,
        eventDidMount: function (info) {
          const colorMap = { 0: "info", 1: "primary", 2: "success", 3: "warning", 4: "danger" };
          const step = info.event.extendedProps.step;
          const colorVar = "var(--" + (colorMap[step] ?? "primary") + ")";
          const timeEl = info.el.querySelector(".fc-event-time");
          const titleEl = info.el.querySelector(".fc-event-title");
          if (timeEl) timeEl.style.color = colorVar;
          if (titleEl) titleEl.style.color = colorVar;
        },
        eventClick: (info) => {
          this.showDate(info.event.id);
        },
        eventMouseEnter: function (info) {},
        eventMouseLeave: function (info) {},
        loading: function (isLoading, view) {
          if (isLoading) {
            // isLoading gives boolean value
          } else {
          }
        },
      };
    },
  },
  mounted() {
    if (window.arole === "a" || window.arole === "s") this.gModalSize = "xl";
    this.game_dates = this.formatDates(this.gamedates);
    const fpPromise = FingerprintJS.load();
    (async () => {
      let fp = await fpPromise;
      let result = await fp.get();
      this.fp = result.visitorId;
    })();
  },
  methods: {
    showAlert(msg, variant, duration = 5) {
      // console.log('showAlert()', msg, variant, duration)
      this.alertVar = variant;
      this.alertMsg = msg;
      this.alert = duration;
    },
    showDate(id) {
      axios.get("/registration/date/get/" + id).then(
        (response) => {
          if (response.data.success === true) {
            this.date = response.data.date;
            this.utcDate = response.data.utcDate;
            this.$bvModal.show("modal-date");
          } else {
            console.log(response.data);
          }
        },
        (error) => {
          console.log(error);
        }
      );
    },
    updateDates(dates) {
      this.game_dates = false;
      this.$nextTick(() => {
        this.game_dates = this.formatDates(dates);
      });
    },
    formatDates(dates) {
      let new_dates = [];
      for (let i in dates) {
        let date = dates[i];
        let admin = "n/a";
        admin = date.admin;
        let colors = { 0: "info", 1: "primary", 2: "success", 3: "warning", 4: "danger" };
        let color = "var(--" + colors[date.step] + ")";

        let num = date.num;

        // Si l'heure est avant 14h, on considère que c'est la soirée de la veille (ex: 00h30 → affiché la veille)
        let hour = parseInt(date.date.substr(11, 2));
        let minute = parseInt(date.date.substr(14, 2));
        let calDate = date.date;
        if (hour < 14) {
          let d = new Date(date.date.replace(" ", "T"));
          d.setDate(d.getDate() - 1);
          let y = d.getFullYear();
          let m = String(d.getMonth() + 1).padStart(2, "0");
          let day = String(d.getDate()).padStart(2, "0");
          calDate = y + "-" + m + "-" + day + " " + date.date.substr(11);
        }

        // Les heures < 14h sont traitées comme heure+24 pour le tri (01:00 → 25:00, après 23:15)
        let sortOrder = hour < 14 ? (hour + 24) * 60 + minute : hour * 60 + minute;

        new_dates.push({
          id: date.id,
          title: (date.step > 0) ? "Step" + date.step : date.title,
          start: calDate.replace(" ", "T"),
          end: calDate
            .replace(" ", "T")
            .replace(calDate.substr(calDate.length - 2, 2), "59"),
          description: "Players: " + num + "/10",
          color: color,
          sortOrder: sortOrder,
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
  .calendar {
    .fc {
      .fc-header-toolbar {
        .fc-addEvent-button {
          font-size: 1.2em;
          padding: 0.2em 0.65em;
        }
        .fc-icon {
          font-size: 1em;
        }
      }
      .fc-view-harness {
        height: 720px !important;
        .fc-event {
          cursor: pointer;
        }
        .fc-daygrid-body {
          height: auto !important;
          table {
            height: auto !important;
            tbody {
              tr {
                height: 115px !important;
                td {
                  overflow-y: auto;
                }
              }
            }
          }
        }
      }
    }
  }
}
</style>