<?php

\Tina4\Get::add("/cms/pages", function (\Tina4\Response $response) {
    return $response (\Tina4\renderTemplate("/content/pages.twig"), HTTP_OK, TEXT_HTML);
});

/**
 * CRUD Prototype Page Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
 * POST @ /path, /path/{id} - create & update
 * DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route("/api/admin/pages", new Page(), function ($action, Page $page, $filter, \Tina4\Request $request) {
    if (isset($request->params["websiteId"])) {
        $websiteId = $request->params["websiteId"];
    }

    switch ($action) {
        case "form":
        case "fetch":
            //Return back a form to be submitted to the create
            (new \Tina4\Event())->trigger("beforePageFetch", [$page, $request]);

            $snippets = (new Snippet())->select("id,name", 1000)->orderBy("name")->asArray();

            if ($action == "form") {
                $title = "Add Page";
                $savePath = TINA4_BASE_URL . "/api/admin/pages";
                $content = \Tina4\renderTemplate("/api/admin/pages/form.twig", ["snippets" => $snippets]);
            } else {
                $title = "Edit Page";
                $savePath = TINA4_BASE_URL . "/api/admin/pages/" . $page->id;
                $content = \Tina4\renderTemplate("/api/admin/pages/form.twig", ["data" => $page, "snippets" => $snippets]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#pageForm').valid() ) { saveForm('pageForm', '" . $savePath . "', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
            break;
        case "read":
            //Return a dataset to be consumed by the grid with a filter

            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            } else {
                $where = "1 = 1";
            }

            $pages = $page->select("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();

            (new \Tina4\Event())->trigger("beforePageRead", [$pages]);

            return $pages;

            break;
        case "create":
            (new \Tina4\Event())->trigger("beforePageCreate", [$page, $request]);
            $page->slug = (new Content())->getSlug($request->data->name);
            $page->dateCreated = date($page->DBA->dateFormat . " H:i:s");
            break;
        case "update":
            (new \Tina4\Event())->trigger("beforePageUpdate", [$page, $request]);

            $page->slug = (new Content())->getSlug($request->data->name);
            $page->dateModified = date($page->DBA->dateFormat . " H:i:s");

            break;
        case "afterCreate":
            (new \Tina4\Event())->trigger("afterPageCreate", [$page, $request]);
            $page->saveBlob("content", $request->params["content"]);

            $page->saveFile("image", "image"); //$_FILES["image"]
            return (object)["httpCode" => 200, "message" => "<script>pageGrid.ajax.reload(null, false); showMessage ('Page Created');</script>"];
            break;
        case "afterUpdate":
            (new \Tina4\Event())->trigger("afterPageUpdate", [$page, $request]);
            $page->saveBlob("content", $request->params["content"]);

            $page->saveFile("image", "image");

            return (object)["httpCode" => 200, "message" => "<script>pageGrid.ajax.reload(null, false); showMessage ('Page Updated');</script>"];
            break;
        case "delete":
            (new \Tina4\Event())->trigger("beforePageDelete", [$page, $request]);

            break;
        case "afterDelete":
            (new \Tina4\Event())->trigger("afterPageDelete", [$page, $request]);
            return (object)["httpCode" => 200, "message" => "<script>pageGrid.ajax.reload(null, false); showMessage ('Page Deleted');</script>"];
            break;
    }
});
