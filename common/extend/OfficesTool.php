<?php


namespace common\extend;



use PHPExcel_IOFactory;

/**
 * office 帮助类
 */
class OfficesTool
{
    /**
     * 构造函数
     * @access public
     * @param array $config 连接信息
     * @return string
     */
    public function __construct(array $config = [])
    {

    }

    /**
     * 读取execl
     * @access public
     * @param string $file 文件名
     * @param string $nullValue 单元格不存在时返回值
     * @param string $calculateFormulas 是否计算单元格公式
     * @param string $formatData 是否将格式应用于单元格值
     * @param string $returnCellRef False - 返回一个简单的行和列的数组，通过从零开始计数
     *      *                                  True  - 返回按其实际行和列ID编制索引的行和列
     * @return boolen|\Generator
     */
    public function readExecl($file = '', $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = true)
    {
        if (is_file($file)) {
            $phpExcel = PHPExcel_IOFactory::load($file);

            $sheetCount = $phpExcel->getSheetCount();

            for ($i = 0; $i < $sheetCount; $i++) {
                yield $phpExcel->getSheet($i)->toArray($nullValue, $calculateFormulas, $formatData, $returnCellRef);
            }
        } else {
            yield false;
        }
    }
}