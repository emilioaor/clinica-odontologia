<script>
import { Bar, mixins } from "vue-chartjs";

export default {
  extends: Bar,
  mixins: [mixins.reactiveData],
  props: ["data"],
  mounted: function() {
    this.renderLineChart();
  },
  methods: {
    renderLineChart: function() {
      let type = new Array();
      let amount = new Array();
      this.data.forEach(payment => {
        type.push(payment.type);
        amount.push(payment.amount);
      });
      this.renderChart(
        {
          labels: type,
          datasets: [
            {
              fillColor: "rgba(172,194,132,0.4)",
              backgroundColor: [
                "#f87979",
                "#EA2027",
                "#006266",
                "#1B1464",
                "#5758BB",
                "#6F1E51",
                '#0080FF',
                '#6495ED',
                '#008B8B',
                '#FF8C00',
                '#9932CC',
                '#1E90FF'

              ],
              data: amount
            }
          ]
        },
        {
          responsive: true,
          maintainAspectRatio: false,
          legend: { display: false },
          title: {
            display: true,
            text: "Porcentaje de pagos"
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  callback: (value, index, values) => {
                    return value + " $";
                  }
                }
              }
            ]
          }
        }
      );
    }
  },
  watch: {
    data: function() {
      this.renderLineChart();
    }
  }
};
</script>