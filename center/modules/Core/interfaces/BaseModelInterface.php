<?php

namespace center\modules\Core\interfaces;

interface BaseModelInterface
{
    //获取搜索字段
    public function getSearchField();
    //获取搜索
    public function getSearchInput();
    //getAttributesList
    public function getAttributesList();
}