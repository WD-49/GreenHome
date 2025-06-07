$(function () {
  // -----------------------------------------------------------------------
  // sales overview
  // -----------------------------------------------------------------------

  // Tìm phần tử mục tiêu cho biểu đồ sales-overview
  var salesOverviewElement = document.querySelector("#sales-overview");

  // Chỉ khởi tạo và render biểu đồ nếu phần tử tồn tại
  if (salesOverviewElement) {
    var options_sales_overview = {
      series: [
        {
          name: "Ample Admin",
          data: [355, 390, 300, 350, 390, 180],
        },
        {
          name: "Pixel Admin",
          data: [280, 250, 325, 215, 250, 310],
        },
      ],
      chart: {
        type: "bar",
        height: 275,
        toolbar: {
          show: false,
        },
        foreColor: "#adb0bb",
        fontFamily: "inherit",
        sparkline: {
          enabled: false,
        },
      },
      grid: {
        show: false,
        borderColor: "transparent",
        padding: {
          left: 0,
          right: 0,
          bottom: 0,
        },
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: "25%",
          endingShape: "rounded",
          borderRadius: 5,
        },
      },
      colors: ["var(--bs-primary)", "var(--bs-secondary)"],
      dataLabels: {
        enabled: false,
      },
      yaxis: {
        show: true,
        min: 100,
        max: 400,
        tickAmount: 3,
      },
      stroke: {
        show: true,
        width: 5,
        lineCap: "butt",
        colors: ["transparent"],
      },
      xaxis: {
        type: "category",
        categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        axisBorder: {
          show: false,
        },
      },
      fill: {
        opacity: 1,
      },
      tooltip: {
        theme: "dark",
      },
      legend: {
        show: false,
      },
    };

    var chart_column_basic = new ApexCharts(
      salesOverviewElement, // Sử dụng biến đã kiểm tra
      options_sales_overview
    );
    chart_column_basic.render();
  } else {
    // (Tùy chọn) Ghi log nếu phần tử không tìm thấy, hữu ích khi debug
    // console.log("Phần tử #sales-overview không tìm thấy trên trang này. Biểu đồ sales overview sẽ không được render.");
  }

  // Nếu bạn có các biểu đồ khác trong file này,
  // hãy áp dụng logic kiểm tra tương tự cho từng biểu đồ.
  // Ví dụ:
  // var anotherChartElement = document.querySelector("#another-chart-id");
  // if (anotherChartElement) {
  //   // ... khởi tạo và render anotherChartElement ...
  // }
});