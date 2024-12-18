imc -load cov_work/scope/test -execcmd 'report -detail -out detailed_coverage_report.txt'


imc -load cov_work/scope/test -execcmd 'report -summary -out coverage_report.txt'


module fcfs_arbiter #(
    parameter NUM_REQ = 4  // Number of requesters
)(
    input logic clk,                 // Clock signal
    input logic resetn,              // Active low reset
    input logic [NUM_REQ-1:0] req,   // Request inputs
    output logic [NUM_REQ-1:0] grant // Grant outputs
);

    integer i;
    always_ff @(posedge clk or negedge resetn) begin
        if (!resetn) begin
            grant <= {NUM_REQ{1'b0}};  // Reset all grants
        end else begin
            grant <= {NUM_REQ{1'b0}};  // Default: No grants
            for (i = 0; i < NUM_REQ; i++) begin
                if (req[i]) begin
                    grant[i] <= 1'b1;  // Grant to the first requester
                    break;
                end
            end
        end
    end

endmodule




interface arbiter_if #(parameter NUM_REQ = 4) (
    input logic clk,
    input logic resetn
);
    logic [NUM_REQ-1:0] req;
    logic [NUM_REQ-1:0] grant;
endinterface





class driver;
    arbiter_if arb_if;

    // Constructor
    function new(arbiter_if arb_if);
        this.arb_if = arb_if;
    endfunction

    // Drive requests
    task drive();
        arb_if.req = $urandom_range(0, 2**arb_if.NUM_REQ - 1);
        @(posedge arb_if.clk);
    endtask
endclass






class monitor;
    arbiter_if arb_if;
    mailbox monitor_mb;

    // Constructor
    function new(arbiter_if arb_if, mailbox monitor_mb);
        this.arb_if = arb_if;
        this.monitor_mb = monitor_mb;
    endfunction

    // Capture outputs
    task monitor_outputs();
        forever begin
            @(posedge arb_if.clk);
            monitor_mb.put(arb_if.grant);
        end
    endtask
endclass






class scoreboard;
    mailbox monitor_mb;
    logic [arbiter_if.NUM_REQ-1:0] expected_grant;

    // Constructor
    function new(mailbox monitor_mb);
        this.monitor_mb = monitor_mb;
    endfunction

    // Compare outputs
    task check_outputs();
        logic [arbiter_if.NUM_REQ-1:0] observed_grant;
        forever begin
            monitor_mb.get(observed_grant);
            // Generate expected grant
            expected_grant = 0;
            foreach (observed_grant[i]) begin
                if (observed_grant[i]) begin
                    expected_grant[i] = 1;
                    break;
                end
            end
            assert (observed_grant == expected_grant)
                else $error("Grant mismatch: Observed = %b, Expected = %b", observed_grant, expected_grant);
        end
    endtask
endclass





module tb_fcfs_arbiter;
    parameter NUM_REQ = 4;

    // Signals
    logic clk;
    logic resetn;

    // Interface instance
    arbiter_if #(NUM_REQ) arb_if (clk, resetn);

    // DUT instance
    fcfs_arbiter #(NUM_REQ) dut (
        .clk(arb_if.clk),
        .resetn(arb_if.resetn),
        .req(arb_if.req),
        .grant(arb_if.grant)
    );

    // Testbench components
    driver drv;
    monitor mon;
    scoreboard sb;
    mailbox monitor_mb;

    // Clock generation
    initial begin
        clk = 0;
        forever #5 clk = ~clk;  // 100 MHz clock
    end

    // Testbench initialization
    initial begin
        resetn = 0;
        #15 resetn = 1;  // Release reset after 15 ns

        // Create instances
        monitor_mb = new();
        drv = new(arb_if);
        mon = new(arb_if, monitor_mb);
        sb = new(monitor_mb);

        // Run tasks
        fork
            drv.drive();
            mon.monitor_outputs();
            sb.check_outputs();
        join
    end

    // Simulation end
    initial begin
        #1000 $finish;
    end

endmodule



