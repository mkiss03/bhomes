# Contact Form - Debug Guide

## What was fixed

1. **Enhanced error logging** throughout the entire contact form flow
2. **Better path resolution** that works in different server environments
3. **Comprehensive error messages** with stack traces
4. **Input validation** with detailed missing field reporting
5. **Database connection verification** at every step

## Files modified

- `/api/contact.php` - Main contact form handler with enhanced error logging
- `/models/Contact.php` - Contact model with detailed SQL error logging
- `/config/database.php` - Database connection with connection attempt logging
- `/api/test_contact.php` - NEW diagnostic script

## How to debug on production server

### Step 1: Test the diagnostic endpoint

Visit: `https://besthomesespana.com/api/test_contact.php`

This will show you:
- PHP version
- File paths (base_path, config path, model path)
- Whether files exist
- Database connection status
- Number of contacts in database

### Step 2: Check error logs

The system now logs to: `/error_log.txt` in the root directory

To view errors on Rackhost:
1. Log in to Rackhost File Manager or FTP
2. Look for `error_log.txt` in the root directory (`/public_html/` or `/htdocs/`)
3. Download and read the file to see detailed error messages

### Step 3: Test contact form submission

1. Fill out the contact form on your website
2. Submit it
3. Check the browser console (F12) for the response
4. The response will now include detailed error information:
   - `success`: true/false
   - `message`: User-friendly error message
   - `error`: Technical error details
   - `file`: File where error occurred
   - `line`: Line number of error
   - `trace`: Full stack trace
   - `missing_fields`: Array of missing required fields (if validation fails)

### Step 4: Common issues and solutions

#### Issue: "Config file not found"
**Solution**: The base path is incorrect. Check the error log for the attempted path.
- If you see `/home/user/bhomes/config/database.php`, the path is wrong for production
- The correct path should be something like `/home/c88384/public_html/config/database.php`

#### Issue: "Database connection failed"
**Solution**: Check database credentials in `/config/database.php`
- Host: `mysql.rackhost.hu`
- Database: `c88384bhe`
- Username: `c88384eszti`
- Password: `Eszter2009`

#### Issue: "create() returned false"
**Solution**: SQL insert failed. Check error log for SQL error details.
- Verify the `contacts` table exists
- Verify table structure matches model
- Check database user permissions

#### Issue: "JSON decode error"
**Solution**: The submitted data is not valid JSON
- Check the JavaScript code sending the request
- Verify Content-Type header is set to `application/json`

## What the enhanced error logging includes

### In api/contact.php:
- Path resolution attempts
- File existence checks
- Class loading verification
- Database connection status
- POST data validation
- JSON parsing errors
- Complete stack traces

### In models/Contact.php:
- Database connection verification
- SQL query logging
- Bound parameter values
- SQL error codes (SQLSTATE, Error Code, Message)
- PDO exception details
- Operation success/failure logging

### In config/database.php:
- Connection attempt logging
- Connection success confirmation
- Connection error details with credentials (for debugging)

## Testing locally

Run the test endpoint locally:
```bash
php -S localhost:8000
```

Then visit: `http://localhost:8000/api/test_contact.php`

## Next steps

1. Visit the test endpoint: `/api/test_contact.php`
2. If test passes, try submitting the contact form
3. Check the browser console for detailed error response
4. If still failing, check `/error_log.txt` on the server
5. Send me the error details from either the console or the log file
