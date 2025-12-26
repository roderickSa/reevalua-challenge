<?php

namespace Tests\Feature;

use App\Core\Game01\Application\Mappers\ReportMapper;
use App\Core\Game01\Application\Usecases\GenerateReportDateUseCase;
use App\Core\Game01\Domain\Contracts\CreditCardRepositoryInterface;
use App\Core\Game01\Domain\Contracts\LoanRepositoryInterface;
use App\Core\Game01\Domain\Contracts\OtherDebtRepositoryInterface;
use App\Core\Game01\Domain\Contracts\XlsxExporterInterface;
use App\Core\Game01\Domain\Models\CreditCard;
use App\Core\Game01\Domain\Models\Loan;
use App\Core\Game01\Domain\Models\OtherDebt;
use App\Core\Game01\Domain\Models\Report;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class GenerateReportDateUseCaseTest extends TestCase
{
    private GenerateReportDateUseCase $useCase;

    /** @var XlsxExporterInterface|\Mockery\MockInterface */
    private XlsxExporterInterface $xlsxExporter;

    /** @var ReportMapper|\Mockery\MockInterface */
    private ReportMapper $reportMapper;

    /** @var LoanRepositoryInterface|\Mockery\MockInterface */
    private LoanRepositoryInterface $loanRepository;

    /** @var CreditCardRepositoryInterface|\Mockery\MockInterface */
    private CreditCardRepositoryInterface $creditCardRepository;

    /** @var OtherDebtRepositoryInterface|\Mockery\MockInterface */
    private OtherDebtRepositoryInterface $otherDebtRepository;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->xlsxExporter = Mockery::mock(XlsxExporterInterface::class);
        $this->reportMapper = Mockery::mock(ReportMapper::class);
        $this->loanRepository = Mockery::mock(LoanRepositoryInterface::class);
        $this->creditCardRepository = Mockery::mock(CreditCardRepositoryInterface::class);
        $this->otherDebtRepository = Mockery::mock(OtherDebtRepositoryInterface::class);

        $this->useCase = new GenerateReportDateUseCase(
            $this->xlsxExporter,
            $this->reportMapper,
            $this->loanRepository,
            $this->creditCardRepository,
            $this->otherDebtRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_generates_report_with_data()
    {
        $year = 2023;
        $month = 12;
        $today = now()->format('Y-m-d');

        $loan = Mockery::mock(Loan::class);
        $creditCard = Mockery::mock(CreditCard::class);
        $otherDebt = Mockery::mock(OtherDebt::class);

        $report = Mockery::mock(Report::class);

        $this->loanRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [$loan], 'last_id' => '1']);

        $this->loanRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, '1')
            ->andReturn(['items' => [], 'last_id' => null]);

        $this->otherDebtRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [$otherDebt], 'last_id' => null]);

        $this->creditCardRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [$creditCard], 'last_id' => null]);

        $this->reportMapper->shouldReceive('fromLoan')
            ->with($loan, $today)
            ->andReturn($report);

        $this->reportMapper->shouldReceive('fromOtherDebt')
            ->with($otherDebt, $today)
            ->andReturn($report);

        $this->reportMapper->shouldReceive('fromCreditCard')
            ->with($creditCard, $today)
            ->andReturn($report);

        $this->xlsxExporter->shouldReceive('export')
            ->once()
            ->with([$report, $report, $report], Mockery::type('string'), Mockery::type('array'));

        $this->useCase->execute($year, $month);

        $this->xlsxExporter->shouldHaveReceived('export')->times(1);

        $this->addToAssertionCount(1);
    }

    public function testExecuteGeneratesReportWithNoData()
    {
        $year = 2023;
        $month = 12;
        $today = now()->format('Y-m-d');

        $this->loanRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [], 'last_id' => null]);

        $this->otherDebtRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [], 'last_id' => null]);

        $this->creditCardRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [], 'last_id' => null]);

        $this->xlsxExporter->shouldNotReceive('export');

        $this->useCase->execute($year, $month);

        $this->xlsxExporter->shouldNotHaveReceived('export');

        $this->addToAssertionCount(1);
    }

    public function testExecuteHandlesChunking()
    {
        $year = 2023;
        $month = 12;
        $today = now()->format('Y-m-d');

        $loans = [];
        for ($i = 0; $i < 150; $i++) {
            $loans[] = Mockery::mock(Loan::class);
        }

        $report = Mockery::mock(Report::class);

        $this->loanRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => array_slice($loans, 0, 100), 'last_id' => '100']);

        $this->loanRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, '100')
            ->andReturn(['items' => array_slice($loans, 100, 50), 'last_id' => null]);

        $this->otherDebtRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [], 'last_id' => null]);

        $this->creditCardRepository->shouldReceive('findChunkByDate')
            ->with($year, $month, 100, null)
            ->andReturn(['items' => [], 'last_id' => null]);

        foreach ($loans as $loan) {
            $this->reportMapper->shouldReceive('fromLoan')
                ->with($loan, $today)
                ->andReturn($report);
        }

        $this->xlsxExporter->shouldReceive('export')
            ->twice()
            ->with(Mockery::type('array'), Mockery::type('string'), Mockery::type('array'));

        $this->useCase->execute($year, $month);

        $this->xlsxExporter->shouldHaveReceived('export')->times(2);

        $this->addToAssertionCount(1);
    }
}
