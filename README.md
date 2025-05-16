# Introduction

This is a simple PHP app that demonstrates instrumentation using Open Telemetry and pushing telemetry to Grafana Cloud using Grafana Alloy.

## Usage

Create a .env file containing your Grafana credentials:

    GC_USERNAME=
    GC_TOKEN=
    GC_ENDPOINT=prod-us-central-0
    GC_ALLOY_USERNAME=
    GC_ALLOY_TOKEN=
    GC_ALLOY_ENDPOINT=
    GC_METRIC_USERNAME=
    GC_METRIC_TOKEN=
    GC_METRIC_ENDPOINT=
    GC_TRACE_USERNAME=
    GC_TRACE_TOKEN=
    GC_TRACE_ENDPOINT=
    GC_LOG_USERNAME=
    GC_LOG_TOKEN=
    GC_LOG_ENDPOINT=
    OTEL_EXPORTER_OTLP_ENDPOINT=
    OTEL_EXPORTER_OTLP_HEADERS=Authorization=Basic your_hashed_token
    GC_OTEL_USERNAME=
    GC_OTEL_TOKEN=

Run the app with ./run.sh and test it with ./load.sh

View the app at http://localhost:8080/ and view Grafana Alloy at http://localhost:12345/
