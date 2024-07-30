# SMS Log Formatter

This PHP script reads SMS logs and delivery status logs from separate text files, parses the data, updates the delivery status, and saves the formatted data into a CSV file.

## Prerequisites

- PHP 7.0 or higher

## Files

- `sms_logs.txt`: File containing SMS logs
- `delivery-status.txt`: File containing delivery status logs
- `process_logs.php`: The PHP script to process the logs and generate the CSV file
- `formatted_sms_logs.csv`: The output CSV file

## Usage

1. **Prepare Input Files**

   - Create `sms_logs.txt` and `delivery-status.txt` in the same directory as the PHP script.
   - Ensure the files contain the correct log format.

2. **Run the Script**

   ```sh
   php process_logs.php
