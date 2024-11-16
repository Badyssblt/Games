## Apache2 Installation

- For Apache users, you need to change some parameters in the `php.ini` file to allow large file uploads.  
  Go to `/etc/php/{yourVersion}/apache2` and edit the `php.ini` file.

Here are the parameters to change:  
- `upload_max_filesize = 5G`  
- `post_max_size = 5G` or `0` (to disable)  
- `max_execution_time = 0`  
- `max_input_time = 0`  
- `memory_limit = 5G`  
- `output_buffering = Off`

Next, you just need to restart Apache2.

Now you are ready to display download progress with Axios or Fetch. It might get stuck at 0 for a while because the server needs to fetch the file from the Google Drive API. Once it retrieves the file, the download will start normally.
