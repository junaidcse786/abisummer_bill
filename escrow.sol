pragma solidity ^0.4.0;

interface tokenRecipient { function receiveApproval(address _from, uint256 _value, address _token, bytes _extraData) external; }

contract TokenERC20 {
    // Public variables of the token
    string public name;
    string public symbol;
    uint8 public decimals = 18;
    // 18 decimals is the strongly suggested default, avoid changing it
    uint256 public totalSupply;

    // This creates an array with all balances
    mapping (address => uint256) public balanceOf;
    mapping (address => mapping (address => uint256)) public allowance;

    // This generates a public event on the blockchain that will notify clients
    event Transfer(address indexed from, address indexed to, uint256 value);

    // This notifies clients about the amount burnt
    event Burn(address indexed from, uint256 value);

    /**
     * Constructor function
     *
     * Initializes contract with initial supply tokens to the creator of the contract
     */
    function TokenERC20() public {
        totalSupply = 21000000 * 10 ** uint256(decimals);  // Update total supply with the decimal amount
        name = "SMSG";                                   // Set the name for display purposes
        symbol = "SMS";                               // Set the symbol for display purposes
        balanceOf[this]=totalSupply;
    }

    /**
     * Internal transfer, only can be called by this contract
     */
    function _transfer(address _from, address _to, uint _value) internal {
        // Prevent transfer to 0x0 address. Use burn() instead
        require(_to != 0x0);
        // Check if the sender has enough
        require(balanceOf[_from] >= _value);
        // Check for overflows
        require(balanceOf[_to] + _value >= balanceOf[_to]);
        // Save this for an assertion in the future
        uint previousBalances = balanceOf[_from] + balanceOf[_to];
        // Subtract from the sender
        balanceOf[_from] -= _value;
        // Add the same to the recipient
        balanceOf[_to] += _value;
        emit Transfer(_from, _to, _value);
        // Asserts are used to use static analysis to find bugs in your code. They should never fail
        assert(balanceOf[_from] + balanceOf[_to] == previousBalances);
    }

    /**
     * Transfer tokens
     *
     * Send `_value` tokens to `_to` from your account
     *
     * @param _to The address of the recipient
     * @param _value the amount to send
     */
    function transfer(address _to, uint256 _value) public {
        _transfer(msg.sender, _to, _value);
    }

    /**
     * Transfer tokens from other address
     *
     * Send `_value` tokens to `_to` on behalf of `_from`
     *
     * @param _from The address of the sender
     * @param _to The address of the recipient
     * @param _value the amount to send
     */
    function transferFrom(address _from, address _to, uint256 _value) public returns (bool success) {
        require(_value <= allowance[_from][msg.sender]);     // Check allowance
        allowance[_from][msg.sender] -= _value;
        _transfer(_from, _to, _value);
        return true;
    }

    /**
     * Set allowance for other address
     *
     * Allows `_spender` to spend no more than `_value` tokens on your behalf
     *
     * @param _spender The address authorized to spend
     * @param _value the max amount they can spend
     */
    function approve(address _spender, uint256 _value) public
        returns (bool success) {
        allowance[msg.sender][_spender] = _value;
        return true;
    }

    /**
     * Set allowance for other address and notify
     *
     * Allows `_spender` to spend no more than `_value` tokens on your behalf, and then ping the contract about it
     *
     * @param _spender The address authorized to spend
     * @param _value the max amount they can spend
     * @param _extraData some extra information to send to the approved contract
     */
    function approveAndCall(address _spender, uint256 _value, bytes _extraData)
        public
        returns (bool success) {
        tokenRecipient spender = tokenRecipient(_spender);
        if (approve(_spender, _value)) {
            spender.receiveApproval(msg.sender, _value, this, _extraData);
            return true;
        }
    }

    /**
     * Destroy tokens
     *
     * Remove `_value` tokens from the system irreversibly
     *
     * @param _value the amount of money to burn
     */
    function burn(uint256 _value) public returns (bool success) {
        require(balanceOf[msg.sender] >= _value);   // Check if the sender has enough
        balanceOf[msg.sender] -= _value;            // Subtract from the sender
        totalSupply -= _value;                      // Updates totalSupply
        emit Burn(msg.sender, _value);
        return true;
    }

    /**
     * Destroy tokens from other account
     *
     * Remove `_value` tokens from the system irreversibly on behalf of `_from`.
     *
     * @param _from the address of the sender
     * @param _value the amount of money to burn
     */
    function burnFrom(address _from, uint256 _value) public returns (bool success) {
        require(balanceOf[_from] >= _value);                // Check if the targeted balance is enough
        require(_value <= allowance[_from][msg.sender]);    // Check allowance
        balanceOf[_from] -= _value;                         // Subtract from the targeted balance
        allowance[_from][msg.sender] -= _value;             // Subtract from the sender's allowance
        totalSupply -= _value;                              // Update totalSupply
        emit Burn(_from, _value);
        return true;
    }
}

contract Escrow is TokenERC20 {

    /**
     Keep track of user score and their number of transactions
    */
    
    struct Score {
        uint256 score;  
        uint256 num_transaction;
    }
    
    /**
     Invoice information
    */
    
    struct Invoice {
        bool created; // to keep track whether invoice already exits
        bool completed;
        bool buyerPaid; // buyer already paid for the transaction?
        bool buyerApprove; // buyer allows seller to withdraw fund
        bool sellerApprove; // seller allows buyer for a refund
        bool escrowTraditional; // which approach to follow? traditional escrow or trustless escrow; true=traditional,false= trustless
        address buyer;
        address seller;
        address escrow;
        uint256 invoiceAmount;
        uint256 mortgage;
        uint deadlineForPaymentBuyerInHoursActual; // payment window in hours
        uint buyerWithdrawRequestTime; // the timestamp when buyer requests to withdraw from the contract
        uint sellerWithdrawRequestTime; // the timestamp when seller requests to withdraw from the contract
        uint invoiceCreatedTime;
    }
    
    struct Dispute{
        bool created;
        bool completed;
        uint toBuyer; // number of votes for the flow of fund to buyer (SELLER DID CHEAT!!)
        uint toSeller; // number of votes for the flow of fund to seller (BUYER DID CHEAT!!)
        uint toBurn; // number of votes for the burning of fund (UNSURE ABOUT WHO CHEATED!!)
        address[] toBuyerVoters; // stack of addresses of voters in favor of Buyer
        address[] toSellerVoters; // stack of addresses of voters in favor of Seller
        address[] toBurnVoters; // stack of addresses of voters in favor of burning
        mapping (address => uint) stakeSince;  // timestamp record of voter's voting time
        mapping (address => uint) votedFor;  // vote record of voter's voting time 0=buyer, 1=seller, 2=burn
        address winner;
        address disputeInitiatedBy; // who initiated dispute
        uint256 reward; // voting winner reward
        uint voteDeadlineInHours; // when does the vote end? in hours
        uint disputeCreatedTime;
    }
    
    modifier suchDisputeExists(string _invoiceID){
        require(dispute[_invoiceID].created==true);
        _;
    }
    /**
     DO NOT LET BUYER AND SELLER VOTE FOR THEMSELVES
    */
    modifier noCheating(string _invoiceID){
        require(invoice[_invoiceID].buyer!=msg.sender && invoice[_invoiceID].seller!=msg.sender);
        _;
    }
    modifier suchInvoiceExists(string _invoiceID){
        require(invoice[_invoiceID].created==true);
        _;
    }
    modifier invoiceStillOpen(string _invoiceID){
        require(invoice[_invoiceID].created==true && invoice[_invoiceID].completed==false);
        _;
    }
    modifier disputeStillOpen(string _invoiceID){
        require(dispute[_invoiceID].created==true && dispute[_invoiceID].completed==false);
        _;
    }
    modifier onlyBuyer(string _invoiceID){
        require(invoice[_invoiceID].buyer==msg.sender);
        _;
    }
    modifier onlySeller(string _invoiceID){
        require(invoice[_invoiceID].seller==msg.sender);
        _;
    }
   
    mapping(string => Invoice) invoice; // invoice record array
    
    mapping(string => Dispute) dispute; // dispute record array
    
    enum fundGoesTo {BUYER, SELLER, BURN} // voting options
    
    mapping(address => Score) public userScore; // user score record array
    
    function requestToken(uint256 _amount) public {
        _transfer(this, msg.sender, _amount);
    }
    /**
     * Intitiate an Invoice
     * by providing all the values
    */
    function createInvoice (
            string _invoiceID, 
            address _buyer, 
            address _escrow, 
            uint256 _invoiceAmount,
            uint256 _mortgage,
            bool _escrowTraditional,
            uint _windowInHours // 24 hours at least 
        ) public returns (bool success){
        
        /**
         check for existing invoice and data validation
        */    
        require(invoice[_invoiceID].created==false);
        require(balanceOf[msg.sender] >= _invoiceAmount+_mortgage && _buyer!=0x0 && _invoiceAmount>0 && _windowInHours>=24);
        
        /**
         check for escrow data validation
        */ 
        if(_escrowTraditional==true)
            require(_escrow!=0x0);

        Invoice storage temp_invoice=invoice[_invoiceID];  //instead of using invoice[_invoiceID] every time, temp_invoice is called
        
        temp_invoice.created=true;
        temp_invoice.seller=msg.sender;
        temp_invoice.buyer=_buyer;
        temp_invoice.escrow=_escrow;
        temp_invoice.invoiceAmount=_invoiceAmount;
        temp_invoice.mortgage=_mortgage;
        temp_invoice.escrowTraditional=_escrowTraditional;
        temp_invoice.buyerPaid=false;
        temp_invoice.deadlineForPaymentBuyerInHoursActual = _windowInHours;
        temp_invoice.invoiceCreatedTime=now;
        
        /**
         in case of the seller providing mortgage, the mortgage is safely stored in the contract
        */ 
        if(_mortgage>0)
            _transfer(msg.sender, this, _mortgage);
            
        return true;
    }
    /**
     When the buyer does not pay the invoice before deadline and the seller has mortgage locked in the contract, this function
     allows the seller to withdraw the mortgage
    */
    function withdrawMortgageAfterNoInvoicePayment (
            string _invoiceID 
        ) public invoiceStillOpen(_invoiceID) onlySeller(_invoiceID) returns (bool success){
        
        Invoice storage temp_invoice=invoice[_invoiceID];    
        require((now > temp_invoice.invoiceCreatedTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && temp_invoice.buyerPaid==false);

        _transfer(this, msg.sender, temp_invoice.mortgage);
        
        return true;
    }
    /**
     This function allows the buyer to pay for the invoice 
     before the deadline
    */
    function payInvoice (
            string _invoiceID, 
            uint256 _invoiceAmount
        ) public invoiceStillOpen(_invoiceID) onlyBuyer(_invoiceID) returns (bool success){
            
        Invoice storage temp_invoice=invoice[_invoiceID];
        require((now <= temp_invoice.invoiceCreatedTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && _invoiceAmount>=temp_invoice.invoiceAmount && temp_invoice.buyerPaid==false);    

        _transfer(msg.sender, this, _invoiceAmount);
        temp_invoice.buyerPaid=true; // buyer just paid, update the ledger
        
        return true;
    }
    /**
     Let the buyer/seller allow one another to withdraw funds from the contract before or after a request is received by one 
     of the parties
    */
    function approveWithdrawal (
            string _invoiceID 
        ) public invoiceStillOpen(_invoiceID) returns (bool success){
        
        Invoice storage temp_invoice=invoice[_invoiceID];
        require(temp_invoice.buyerPaid==true); // buyer must have paid  
        
        address buyer = temp_invoice.buyer;
        address seller = temp_invoice.seller;
        
        if(seller==msg.sender)
            temp_invoice.sellerApprove=true;
        
        else if(buyer==msg.sender)
            temp_invoice.buyerApprove=true;
            
        return true;
    } 
    /**
     One of the parties wants to withdraw their fund (nicely!) from the contract;
     only accessible by buyer and seller with/out dispute
    */
    function withdrawFund (
            string _invoiceID 
        ) public invoiceStillOpen(_invoiceID) returns (bool success){
            
        Invoice storage temp_invoice=invoice[_invoiceID];
        address buyer = temp_invoice.buyer;
        address seller = temp_invoice.seller;
        
        require((seller==msg.sender || buyer==msg.sender) && temp_invoice.buyerPaid==true);
        
        uint256 _invoiceAmount=temp_invoice.invoiceAmount;
        uint256 _mortgage=temp_invoice.mortgage;
        
        if(dispute[_invoiceID].created==false){ // if there is no dispute so far
            bool successfulTransfer = false;
            
            if(seller==msg.sender){ 
                /**
                 if there is buyer approval or window (the same window given to the buyer from the seller)
                 has passed after the request from seller i.e. no complaints so far
                 and of course the 24 hours restriction is over
                */
                if((temp_invoice.buyerApprove || now > temp_invoice.sellerWithdrawRequestTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && now > temp_invoice.invoiceCreatedTime + 24 hours){
                    _transfer(this, seller, _invoiceAmount + _mortgage); // mortgage is returned along with the actual invoice amount
                    successfulTransfer=true;
                }
                else
                    temp_invoice.sellerWithdrawRequestTime=now; // requesting permission from the buyer
            }
            else if(buyer==msg.sender){
                if((temp_invoice.sellerApprove || now > temp_invoice.buyerWithdrawRequestTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && now > temp_invoice.invoiceCreatedTime + 24 hours){
                    _transfer(this, buyer, _invoiceAmount);
                    successfulTransfer=true;
                }
                else
                    temp_invoice.buyerWithdrawRequestTime=now; // requesting permission from the seller
            }
            if(successfulTransfer){
                /**
                 increase transaction number of both parties and score them good
                 for honest transactions
                */
                userScore[buyer].num_transaction++;
                userScore[seller].num_transaction++;
                userScore[seller].score += _invoiceAmount + _mortgage; // extra score for seller since mortgage was in play
                userScore[buyer].score += _invoiceAmount;
                temp_invoice.completed=true; // complete transaction and no more interaction for this invoice
            }
        }
        else if(dispute[_invoiceID].created==true && dispute[_invoiceID].completed==false && temp_invoice.escrowTraditional==false) // call the function if there is dispute and trustless escrow is chosen
            calculateWinnerandDisperseFund(_invoiceID); 
            
       /* else if(dispute[_invoiceID].created==true && temp_invoice.escrowTraditional==true) // call the function if there is dispute and tradtitonal escrow is chosen
            escrowDisperseFund(_invoiceID);  */   
        return true;
    }
    /**
     Let the buyer/seller allow one another to withdraw funds from the contract before or after a request is received by one 
     of the parties
    */
    function escrowSolveDipsute (
            string _invoiceID,
            fundGoesTo _fundgoesTo
        ) public invoiceStillOpen(_invoiceID) suchDisputeExists(_invoiceID) disputeStillOpen(_invoiceID) returns (bool success){
        
        Invoice storage temp_invoice=invoice[_invoiceID];
        require(temp_invoice.buyerPaid==true); // buyer must have paid  
        
        address buyer = temp_invoice.buyer;
        address seller = temp_invoice.seller;
        
        if(seller==msg.sender)
            temp_invoice.sellerApprove=true;
        
        else if(buyer==msg.sender)
            temp_invoice.buyerApprove=true;
            
        return true;
    }
    /**
     Initiate dispute with the values given
     vote deadline must be at least 24 hours and functions 
     must be triggered by either the seller
     or the buyer
    */
    function initiateDispute (
            string _invoiceID, 
            uint256 _reward, 
            uint _voteDeadlineInHours
        ) public invoiceStillOpen(_invoiceID) returns (bool success){
            
        require(dispute[_invoiceID].created==false); // check for existing dispute
        require((invoice[_invoiceID].buyer==msg.sender || invoice[_invoiceID].seller==msg.sender) && _voteDeadlineInHours>=24 && ( 0 < _reward && _reward <= invoice[_invoiceID].invoiceAmount)  && invoice[_invoiceID].buyerPaid==true);
        
        Dispute storage temp_dispute=dispute[_invoiceID];
        
        temp_dispute.created=true;
        temp_dispute.reward=_reward;
        temp_dispute.voteDeadlineInHours = _voteDeadlineInHours;
        temp_dispute.disputeInitiatedBy=msg.sender;
        temp_dispute.disputeCreatedTime = now;

        return true;
    }
    /**
     Vote for a dispute 
     available for anybody to participate
     anybody with some requirements
    */
    function voteWithStake (
            string _invoiceID, 
            fundGoesTo _fundgoesTo
        ) public invoiceStillOpen(_invoiceID) suchDisputeExists(_invoiceID) disputeStillOpen(_invoiceID) noCheating(_invoiceID) returns (bool success){
            
        Dispute storage temp_dispute=dispute[_invoiceID];
        
        /**
         check dispute deadline, user score, reward amount availability
         and check if already voted with the same account
        */
        require(now <= temp_dispute.disputeCreatedTime + temp_dispute.voteDeadlineInHours * 1 hours && balanceOf[msg.sender]>=temp_dispute.reward && userScore[msg.sender].score>=temp_dispute.reward && temp_dispute.stakeSince[msg.sender]==0);
        
        _transfer(msg.sender, this, temp_dispute.reward); // store the stakes in the contract
        
        temp_dispute.stakeSince[msg.sender]=now; // store the staking timestamp
        temp_dispute.votedFor[msg.sender]=uint(_fundgoesTo); // store which option is voted by the voter

        /**
         increase the number of votes according to vote
         and store the address of the voter 
        */
        if(uint(_fundgoesTo)==0){
            temp_dispute.toBuyerVoters.push(msg.sender);
            temp_dispute.toBuyer++;
        }
        else if(uint(_fundgoesTo)==1){
            temp_dispute.toSellerVoters.push(msg.sender);
            temp_dispute.toSeller++;
        }
        else if(uint(_fundgoesTo)==2){
            temp_dispute.toBurnVoters.push(msg.sender);
            temp_dispute.toBurn++;  
        }
        return true;
    }
    /**
     Voter wishes to withdraw the stake;
     only allowed if 24 hours have passed since staking
    */
    function withdrawStake (
            string _invoiceID 
        ) public suchInvoiceExists(_invoiceID) suchDisputeExists(_invoiceID) returns (bool success){
            
        Dispute storage temp_dispute=dispute[_invoiceID];
        
        require(now > temp_dispute.stakeSince[msg.sender] + 24 hours); // check for 24 hours restriction
        
        if(temp_dispute.completed && temp_dispute.winner==msg.sender) // check if dispute is over and winner has already been chosen
           _transfer(msg.sender, this, 2 * temp_dispute.reward); // if won the random lottery get back stake along with the reward
           
        else {
            if(!temp_dispute.completed){
                /**
                 If the dispute is not over, then return the stake
                 but also decrease the number of according votes
                */
                if(temp_dispute.votedFor[msg.sender]==0)
                    temp_dispute.toBuyer--;
                    
                else if(temp_dispute.votedFor[msg.sender]==1)
                    temp_dispute.toSeller--;
                    
                else if(temp_dispute.votedFor[msg.sender]==2)
                    temp_dispute.toBurn--; 
            }
            _transfer(msg.sender, this, temp_dispute.reward);
        }
        return true;
    }
    /**
     This function settles the dispute and decides the flow of the fund
     function can be triggered by functions only internal
     in this case triggered by the withdrawFund function (only buyer or seller)
    */
    function calculateWinnerandDisperseFund(string _invoiceID) internal invoiceStillOpen(_invoiceID) suchDisputeExists(_invoiceID) disputeStillOpen(_invoiceID){
        
        Dispute storage temp_dispute=dispute[_invoiceID];
        Invoice storage temp_invoice=invoice[_invoiceID];
        
        require(invoice[_invoiceID].buyer==msg.sender || invoice[_invoiceID].seller==msg.sender); // double check
        
        //check if vote deadline has reached
        require(now > temp_dispute.disputeCreatedTime + temp_dispute.voteDeadlineInHours * 1 hours);
        
        address buyer = temp_invoice.buyer;
        address seller = temp_invoice.seller;
        uint256 _invoiceAmount = temp_invoice.invoiceAmount;
        uint256 _mortgage = temp_invoice.mortgage;
        uint256 _reward = temp_dispute.reward;
        bool transactionCompleted = false;
        
        uint winningOption = 1; // 1 = buyer , 2 = seller , 3 = burn the fund, since unsure about who cheated
        
        if(temp_dispute.toBuyer < temp_dispute.toSeller) // find the highest number of voting option voted
            winningOption = 2;
            
        else if(temp_dispute.toSeller < temp_dispute.toBurn) // find the highest number of voting option voted
            winningOption = 3; 
            
        /**
        *Choose the random winner based on the winning vote after counting
         the winner can later collect his/her reward while withdrawing stakes
        */
        chooseRandomWinner(_invoiceID, winningOption);
        
        if(winningOption == 1){ // if buyer is voted to be honest
            _transfer(this, buyer, _invoiceAmount + _mortgage - _reward); // give the buyer his/her refund and the mortgage (for all the trouble), because the seller was dishonest 
            // reward is subtracted and saved for the winner
            userScore[buyer].num_transaction++;
            userScore[seller].num_transaction++;
            userScore[seller].score -= _invoiceAmount; // bad score for seller for dishonesty
            userScore[buyer].score += _invoiceAmount;
            transactionCompleted=true;
        }
        else if(winningOption == 2){
            _transfer(this, seller, _invoiceAmount + _mortgage - _reward); // give the seller his/her refund and the mortgage back, because the buyer was dishonest 
            // reward is subtracted and saved for the winner
            userScore[buyer].num_transaction++;
            userScore[seller].num_transaction++;
            userScore[buyer].score -= _invoiceAmount; // bad score for buyer for dishonesty
            userScore[seller].score += _invoiceAmount + _mortgage; // mortgage mojo for the seller
            transactionCompleted=true;
        }
        else if(winningOption == 3){
            require(balanceOf[this] >= _invoiceAmount + _mortgage - _reward);   // check if invoice amount and mortgage still stored in the contract
            // reward is still subtracted and saved for the winner for the made efforts
            balanceOf[this] -= (_invoiceAmount + _mortgage - _reward);         // burn the invoice amount and mortgage stored in the contract
            totalSupply -= (_invoiceAmount + _mortgage - _reward);            // Updates totalSupply

            userScore[buyer].num_transaction++;
            userScore[seller].num_transaction++; //no scoring, since no decision could be taken
            transactionCompleted=true;
        }
        /**
        *If there were no votes or the voting options has the 
        *same number of votes 
        */
        else if(temp_dispute.toSeller==temp_dispute.toBuyer && temp_dispute.toSeller==temp_dispute.toBurn){
            temp_invoice.invoiceCreatedTime=now; // reinitiate the vote again for the same vote duration
        }
        /**
         The matter is settled and the transaction is completed
        */
        if(transactionCompleted){
            temp_invoice.completed=true;
            temp_dispute.completed=true;
        }
    }
    /**
    *choose the random Winner of the winning vote option
    */
    function chooseRandomWinner (
            string _invoiceID,
            uint _winningOption
        ) internal invoiceStillOpen(_invoiceID) suchDisputeExists(_invoiceID) disputeStillOpen(_invoiceID){
        
        require(invoice[_invoiceID].buyer==msg.sender || invoice[_invoiceID].seller==msg.sender); // double check
        Dispute storage temp_dispute=dispute[_invoiceID];
        uint _randomIndex=0; // choose the random index
        
        if(_winningOption==1)
            temp_dispute.winner=temp_dispute.toBuyerVoters[_randomIndex]; // take one random voter from the array of voters in favor of buyer
            
        else if(_winningOption==2)
            temp_dispute.winner=temp_dispute.toSellerVoters[_randomIndex]; // take one random voter from the array of voters in favor of seller
            
        else if(_winningOption==3)
            temp_dispute.winner=temp_dispute.toBurnVoters[_randomIndex]; // take one random voter from the array of voters in favor of no-one and burn the fund
    }
}