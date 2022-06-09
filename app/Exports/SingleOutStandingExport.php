<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use API;

class SingleOutStandingExport implements FromCollection, WithHeadings
{
    use Exportable;
    protected $no_document;
    public function __construct($no_document)
    {
        $this->no_document = $no_document;
    }


    public function collection()
    {
        $service = API::exec(array(
            'request' => 'GET',
            'method' => "tr_materials/" . $this->no_document
        ));

        foreach($service->data as $fmdb) {
            $param[] = array(
                "material_number" => $fmdb->no_material,
                "industri_sector" => $fmdb->industri_sector,
                "material_type" => $fmdb->material_type,
                "plant" => $fmdb->plant,
                "store_loc" => $fmdb->store_loc,
                "sales_org" => $fmdb->sales_org,
                "dist_channel" => $fmdb->dist_channel,
                "material_name" => $fmdb->material_name,
                "uom" => $fmdb->uom,
                "mat_group" => $fmdb->mat_group,
                "division" => $fmdb->division,
                "item_cat_group" => $fmdb->item_cat_group,
                "gross_weight" => $fmdb->gross_weight,
                "net_weight" => $fmdb->net_weight,
                "volume" => $fmdb->volume,
                "size_dimension" => $fmdb->size_dimension,
                "weight_unit" => $fmdb->weight_unit,
                "volume_unit" => $fmdb->volume_unit,
                "cash_discount" => $fmdb->cash_discount,
                "tax_classification" => $fmdb->tax_classification,
                "account_assign" => $fmdb->account_assign,
                "general_item" => $fmdb->general_item,
                "avail_check" => $fmdb->avail_check,
                "transportation_group" => $fmdb->transportation_group,
                "loading_group" => $fmdb->loading_group,
                "profit_center" => $fmdb->profit_center,
                "mrp_type" => $fmdb->mrp_type,
                "mrp_controller" => $fmdb->mrp_controller,
                "period_sle" => $fmdb->period_sle,
                "valuation_class" => $fmdb->valuation_class,
                "price_unit" => $fmdb->price_unit,
                "price_estimate" => $fmdb->price_estimate
            );
        }


        return collect($param);
    }

    public function headings(): array
    {

        return [
            'material number',
            'industri sector',
            'material type',
            'plant',
            'store loc',
            'sales org',
            'dist channel',
            'material name',
            'uom',
            'mat group',
            'division',
            'item cat group',
            'gross weight',
            'net weight',
            'volume',
            'size dimension',
            'weight unit',
            'volume unit',
            'cash discount',
            'tax classification',
            'account assign',
            'general item',
            'avail check',
            'transportation group',
            'loading group',
            'profit center',
            'mrp type',
            'mrp controller',
            'period sle',
            'valuation class',
            'price unit',
            'price estimate',

        ];
    }
}

