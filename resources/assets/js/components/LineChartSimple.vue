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
      this.renderChart(
        {
          labels: [`Total de pagos ${this.data[0].totalPayments}$`, this.data[0].titleExpenses],
          datasets: [
            {
              fillColor: "rgba(172,194,132,0.4)",
              backgroundColor: [
                "#32CD32",
                "#EA2027",
                "#006266",
                "#1B1464",
                "#5758BB",
                "#6F1E51"
              ],
              data: [
                this.data[0].totalPayments,
                this.data[0].totalExpenses
              ]
            }
          ]
        },
        {
          responsive: true,
          maintainAspectRatio: false,
          legend: { display: false },
          title: {
            display: true,
            text: `Pago vs Gastos`
          },
          scales: {
            yAxes: [
              {
                ticks: {
                  callback: (value, index, values) => {
                    return value + "$";
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