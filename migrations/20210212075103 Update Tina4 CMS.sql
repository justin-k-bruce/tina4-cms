alter table `page`
    add headers text after image;
alter table `page`
    add header_content text after headers;
alter table `page`
    add footer text after content;