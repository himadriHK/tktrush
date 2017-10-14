<?php
$include_dir = "include"; 
include "$include_dir/config.php";
include "$include_dir/function.php";
include "$include_dir/pagination.php";
include "security.php";
extract($_REQUEST);
extract($_POST);
ini_set('display_errors', 1);
$pageName = "Reports";
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<title><?php echo $config['SITE_NAME']?> | Reports Management</title>
<?php include_once("top.php")?>
 <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2017.3.913/styles/kendo.common-material.min.css" />
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2017.3.913/styles/kendo.rtl.min.css" />
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2017.3.913/styles/kendo.material.mobile.min.css" />
	<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2017.3.913/styles/kendo.material.min.css" />
    
    <script src="js/kendo.web.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/jszip/2.4.0/jszip.js"></script>
	<script src="http://kendo.cdn.telerik.com/2017.3.913/js/kendo.all.min.js"></script>
	
</head>
<body>
<div class="layout">
	<?php
    include_once("navbar.php");
	?>
	<div class="main-wrapper">
		<div class="container-fluid">
			<div class="row-fluid ">
				<div class="span12">
					<div class="primary-head">
						<h3 class="page-header">Reports Management</h3>
					</div>
				</div>
			</div>
			
			
            <?php
            function addRecordForm() {}
			
			function editRecordForm($id) {}
			
			function listRecords() {
				
				global $config, $conn,$database;
				require_once 'Kendo/Autoload.php';
				$data=$database->query("select p.name Partner,e.title Title,det.category Category,det.subcategory Subcategory,det.price Price,count(*) as tickets_sold,(count(*)*det.price) total_amount,sum(det.commission) commission,sum(det.partner_comm) partner_commission,sum(det.DTCM_charges) DTCM_charges from ticket_orders o left outer join partners p on o.partner_id=p.spid, events e, order_details det where e.tid=o.tid and o.payment_status='paid' and o.oid=det.orderid group by p.name,e.title,det.category,det.subcategory,det.price")->fetchAll(PDO::FETCH_ASSOC);
//var_dump($data);
	$model = new \Kendo\Data\DataSourceSchemaModel();

    // Create a field for the 'ProductName' column of the 'Products' table
   // $titleField = new \Kendo\Data\DataSourceAggregateItem();
   // $titleField->type('string');
   //
   // // Create a field for the 'UnitPrice' column of the 'Products' table
   // $unitPriceField = new \Kendo\Data\DataSourceAggregateItem('titcket_price');
   // $unitPriceField->type('number');

    // Create a field for the 'Discontinued' column of the 'Products' table
    $ticketsSoldField = new \Kendo\Data\DataSourceAggregateItem();
    $ticketsSoldField->field('tickets_sold')->aggregate('sum');
    
    $totalAmount=new \Kendo\Data\DataSourceAggregateItem();
    $totalAmount->field('total_amount')->aggregate('sum');
	
	$commAmount=new \Kendo\Data\DataSourceAggregateItem();
    $commAmount->field('commission')->aggregate('sum');
	
	$partcommAmount=new \Kendo\Data\DataSourceAggregateItem();
    $partcommAmount->field('partner_commission')->aggregate('sum');
	
	$dtcmAmount=new \Kendo\Data\DataSourceAggregateItem();
    $dtcmAmount->field('DTCM_charges')->aggregate('sum');

    //$model->addField($titleField, $unitPriceField, $ticketsSoldField);

    // Create the schema
    $schema = new \Kendo\Data\DataSourceSchema();

    // Set its model
    $schema->data($data)
       ->errors('errors')
       ->groups('groups')
       ->aggregates('aggregates')
       ->total('total');

    // Create the data source
    $dataSource = new \Kendo\Data\DataSource();

	$group = new \Kendo\Data\DataSourceGroupItem();
	//$aggregate = new \Kendo\Data\DataSourceGroupItemAggregate();
	//$aggregate->aggregate('sum');
	$group->field('Title')->addAggregate($ticketsSoldField)->addAggregate($totalAmount)->addAggregate($commAmount)->addAggregate($partcommAmount)->addAggregate($dtcmAmount);
	
	$group2 = new \Kendo\Data\DataSourceGroupItem();
	//$aggregate = new \Kendo\Data\DataSourceGroupItemAggregate();
	//$aggregate->aggregate('sum');
	$group2->field('Partner')->addAggregate($ticketsSoldField)->addAggregate($totalAmount)->addAggregate($commAmount)->addAggregate($partcommAmount)->addAggregate($dtcmAmount);
	
	
    // Specify the schema and data
    $dataSource->data($data)//->serverGrouping(true)
		   ->addGroupItem($group)
		   ->addGroupItem($group2)
		   ->pageSize(10);
		   //->addAggregateItem($ticketsSoldField);
		   //->schema($schema);
			   
	

	
	$grid = new \Kendo\UI\Grid('grid');
	$columnFilterable = new \Kendo\UI\GridColumnFilterable();
$columnFilterable->multi(true)->search(true);
    
    $titleField = new \Kendo\UI\GridColumn();
    $titleField->field('Title')
                      ->title('Title')->filterable($columnFilterable);
    
    $partnerField = new \Kendo\UI\GridColumn();
    $partnerField->field('Partner')
    ->title('Partner')->filterable($columnFilterable);
    
    $catField = new \Kendo\UI\GridColumn();
    $catField->field('Category')
    ->title('Category')->filterable($columnFilterable);
    
    $subcatField = new \Kendo\UI\GridColumn();
    $subcatField->field('Subcategory')
    ->title('Subcategory')->filterable($columnFilterable);
    
    $unitPriceColumn = new \Kendo\UI\GridColumn();
    $unitPriceColumn->field('Price')
                    ->format('{0} AED')
                    ->title('Ticket Price')->filterable($columnFilterable);
    
    $ticketsSoldField = new \Kendo\UI\GridColumn();
    $ticketsSoldField->field('tickets_sold')->title('Tickets')->groupFooterTemplate('Total: #=sum#')->filterable($columnFilterable);
    
    $totalAmount=new \Kendo\UI\GridColumn();
    $totalAmount->field('total_amount')->title('Total')->groupFooterTemplate('Amount: #=sum# AED')->format('{0} AED')->filterable($columnFilterable);
    
	$commAmount=new \Kendo\UI\GridColumn();
    $commAmount->field('commission')->title('Comm')->groupFooterTemplate('Amount: #=sum# AED')->format('{0} AED')->filterable($columnFilterable);
    
	$partcommAmount=new \Kendo\UI\GridColumn();
    $partcommAmount->field('partner_commission')->title('Partner Comm')->groupFooterTemplate('Amount: #=sum# AED')->format('{0} AED')->filterable($columnFilterable);
	
	$dtcmAmount=new \Kendo\UI\GridColumn();
    $dtcmAmount->field('DTCM_charges')->title('DTCM Comm')->groupFooterTemplate('Amount: #=sum# AED')->format('{0} AED')->filterable($columnFilterable);
	
	//$unitsInStockCount = new \Kendo\Data\DataSourceAggregateItem();
	//$unitsInStockCount->field("tickets_sold")->aggregate("count");
	$pageable = new \Kendo\UI\GridPageable();
$pageable->alwaysVisible(true)
         ->pageSizes(array(5, 10, 20, 100));

		 $margin = new \Kendo\UI\GridPdfMargin();
$margin->top("2cm")
       ->left("1cm")
       ->right("1cm")
       ->bottom("1cm");
		 $pdf = new \Kendo\UI\GridPdf();
$pdf->allPages(true)
    ->avoidLinks(true)
    ->paperSize("A4")
    ->margin($margin)
    ->landscape(true)
    ->repeatHeaders(true)
    ->templateId("page-template")
    ->scale(0.8)
    ->fileName('Export.pdf')
    ->proxyURL('pdf-export.php?type=save');
	
    $grid->pdf($pdf)->addToolbarItem(new \Kendo\UI\GridToolbarItem('pdf'))
    ->addColumn($catField,$subcatField,$unitPriceColumn,$ticketsSoldField,$totalAmount,$commAmount,$partcommAmount,$dtcmAmount)->dataSource($dataSource)
    ->sortable(true)->groupable(false)->filterable(true)->columnMenu(false)->pageable($pageable);
	
	echo $grid->render();
	
			}
			?>

            <?php
            switch(@$f) {
			
			case 2;
				if(isset($id) && !empty($id) && isset($status) && !empty($status)) {
					$data=array(
						"payment_status"=>sqlSafe(trim(strip_tags($status)))
						);
					
					editItem(" ticket_orders", $data, $id, "oid");	
				}
				listRecords();
			break;
			
			case 3;
				listRecords();
			break;
			
			case 4;
				deleteListItem($id, "subscribes", "id");
				listRecords();
			break;	
		
			case 7;
				$delAnn = isset($delAnn)?$delAnn:array();
				actionSelected($delAnn, "subscribes", "id");
				listRecords();
			break;
		  
			default;
				listRecords();
			break;
			}
			?>
            
            
		</div>
	</div>
	<?php include_once("footer.php")?>
</div>
<style>
    /*
        Use the DejaVu Sans font for display and embedding in the PDF file.
        The standard PDF fonts have no support for Unicode characters.
    */
    .k-grid {
        font-family: "DejaVu Sans", "Arial", sans-serif;
    }

    /* Hide the Grid header and pager during export */
    .k-pdf-export .k-grid-toolbar,
    .k-pdf-export .k-pager-wrap
    {
        display: none;
    }
</style>

<!-- Load Pako ZLIB library to enable PDF compression -->
<script src="https://kendo.cdn.telerik.com/2017.3.913/js/pako_deflate.min.js"></script>

<script type="x/kendo-template" id="page-template">
  <div class="page-template">
    <div class="header">
      <div style="float: right">Page #: pageNum # of #: totalPages #</div>
      Multi-page grid with automatic page breaking
    </div>
    <div class="watermark">tktrush</div>
    <div class="footer">
      Page #: pageNum # of #: totalPages #
    </div>
  </div>
</script>

<style type="text/css">
    /* Page Template for the exported PDF */
    .page-template {
      font-family: "DejaVu Sans", "Arial", sans-serif;
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
    }
    .page-template .header {
      position: absolute;
      top: 30px;
      left: 30px;
      right: 30px;
      border-bottom: 1px solid #888;
      color: #888;
    }
    .page-template .footer {
      position: absolute;
      bottom: 30px;
      left: 30px;
      right: 30px;
      border-top: 1px solid #888;
      text-align: center;
      color: #888;
    }
    .page-template .watermark {
      font-weight: bold;
      font-size: 400%;
      text-align: center;
      margin-top: 30%;
      color: #aaaaaa;
      opacity: 0.1;
      transform: rotate(-35deg) scale(1.7, 1.5);
    }

    /* Content styling */
    .customer-photo {
        display: inline-block;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-size: 32px 35px;
        background-position: center center;
        vertical-align: middle;
        line-height: 32px;
        box-shadow: inset 0 0 1px #999, inset 0 0 10px rgba(0,0,0,.2);
        margin-left: 5px;
    }

    .customer-name {
        display: inline-block;
        vertical-align: middle;
        line-height: 32px;
        padding-left: 3px;
    }
</style>
</body>