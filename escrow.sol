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

    struct Score {
        uint256 score;
        uint256 num_transaction;
    }
    
    struct Invoice {
        bool created;
        bool completed;
        bool buyerPaid;
        bool buyerApprove;
        bool sellerApprove;
        bool escrowTraditional;
        address buyer;
        address seller;
        address escrow;
        uint256 invoiceAmount;
        uint256 mortgage;
        uint deadlineForPaymentBuyerInHoursActual;
        uint buyerWithdrawRequestTime;
        uint sellerWithdrawRequestTime;
        uint invoiceCreatedTime;
    }
    
    struct Dispute{
        bool created;
        bool completed;
        uint toBuyer;
        uint toSeller;
        uint toBurn;
        address[] toBuyerVoters;
        address[] toSellerVoters;
        address[] toBurnVoters;
        mapping (address => uint) stakeSince;
        mapping (address => uint) votedFor;
        address winner;
        address disputeInitiatedBy;
        uint256 reward;
        uint voteDeadlineInHours;
        uint disputeCreatedTime;
    }
    
    modifier suchDisputeExists(string _invoiceID){
        require(dispute[_invoiceID].created==true);
        _;
    }
    modifier noCheating(string _invoiceID){
        require(invoice[_invoiceID].buyer!=msg.sender && invoice[_invoiceID].seller!=msg.sender);
        _;
    }
    modifier suchInvoiceExists(string _invoiceID){
        require(invoice[_invoiceID].created==true);
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
   
    mapping(string => Invoice) invoice;
    
    mapping(string => Dispute) dispute;
    
    enum fundGoesTo {BUYER, SELLER, BURN}
    
    mapping(address => Score) public userScore;
    
    function requestToken(uint256 _amount) public {
        _transfer(this, msg.sender, _amount);
    }

    function createInvoice (
            string _invoiceID, 
            address _buyer, 
            address _escrow, 
            uint256 _invoiceAmount,
            uint256 _mortgage,
            bool _escrowTraditional,
            uint _windowInHours // 24 hours at least 
        ) public returns (bool success){
            
        require(invoice[_invoiceID].created==false);
        require(balanceOf[msg.sender] >= _invoiceAmount+_mortgage && _buyer!=0x0 && _invoiceAmount>0 && _windowInHours>=24);
        
        if(_escrowTraditional==true)
            require(_escrow!=0x0);

        Invoice storage temp_invoice=invoice[_invoiceID];
        
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
        
        if(_mortgage>0)
            _transfer(msg.sender, this, _mortgage);
            
        return true;
    }
    
    function withdrawMortgageAfterNoInvoicePayment (
            string _invoiceID 
        ) public suchInvoiceExists(_invoiceID) onlySeller(_invoiceID) returns (bool success){
        
        Invoice storage temp_invoice=invoice[_invoiceID];    
        require((now > temp_invoice.invoiceCreatedTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && temp_invoice.buyerPaid==false);

        _transfer(this, msg.sender, temp_invoice.mortgage);
        
        return true;
    }
    
    function payInvoice (
            string _invoiceID, 
            uint256 _invoiceAmount
        ) public suchInvoiceExists(_invoiceID) onlyBuyer(_invoiceID) returns (bool success){
            
        Invoice storage temp_invoice=invoice[_invoiceID];
        require((now <= temp_invoice.invoiceCreatedTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && _invoiceAmount==temp_invoice.invoiceAmount);    

        _transfer(msg.sender, this, _invoiceAmount);
        temp_invoice.buyerPaid=true;
        
        return true;
    }
    
    function approveWithdrawal (
            string _invoiceID 
        ) public suchInvoiceExists(_invoiceID) returns (bool success){
        
        Invoice storage temp_invoice=invoice[_invoiceID];
        require(temp_invoice.buyerPaid==true  && temp_invoice.completed==false);
        
        address buyer = temp_invoice.buyer;
        address seller = temp_invoice.seller;
        
        if(seller==msg.sender)
            temp_invoice.sellerApprove=true;
        
        else if(buyer==msg.sender)
            temp_invoice.buyerApprove=true;
            
        return true;
    } 
    
    function withdrawFund (
            string _invoiceID 
        ) public suchInvoiceExists(_invoiceID) returns (bool success){
            
        Invoice storage temp_invoice=invoice[_invoiceID];
        require(temp_invoice.buyerPaid==true && temp_invoice.completed==false);
        
        address buyer = temp_invoice.buyer;
        address seller = temp_invoice.seller;
        uint256 _invoiceAmount=temp_invoice.invoiceAmount;
        uint256 _mortgage=temp_invoice.mortgage;
        
        if(dispute[_invoiceID].created==false){
            bool successfulTransfer = false;
            
            if(seller==msg.sender){
                if((temp_invoice.buyerApprove || now > temp_invoice.sellerWithdrawRequestTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && now > temp_invoice.invoiceCreatedTime + 24 hours){
                    _transfer(this, seller, _invoiceAmount + _mortgage);
                    successfulTransfer=true;
                }
                else
                    temp_invoice.sellerWithdrawRequestTime=now;
            }
            else if(buyer==msg.sender){
                if((temp_invoice.sellerApprove || now > temp_invoice.buyerWithdrawRequestTime + temp_invoice.deadlineForPaymentBuyerInHoursActual * 1 hours) && now > temp_invoice.invoiceCreatedTime + 24 hours){
                    _transfer(this, buyer, _invoiceAmount);
                    successfulTransfer=true;
                }
                else
                    temp_invoice.buyerWithdrawRequestTime=now;
            }
            if(successfulTransfer){
                userScore[buyer].num_transaction++;
                userScore[seller].num_transaction++;
                userScore[seller].score += _invoiceAmount + _mortgage;
                userScore[buyer].score += _invoiceAmount;
                temp_invoice.completed=true;
            }
        }
        else
            calculateWinnerandDisperseFund(_invoiceID);
            
        return true;
    }
    
    function calculateWinnerandDisperseFund(string _invoiceID) internal returns(bool success){
        
        Dispute storage temp_dispute=dispute[_invoiceID];
        Invoice storage temp_invoice=invoice[_invoiceID];
        
        require(temp_invoice.completed==false && temp_dispute.completed==false && now > temp_dispute.disputeCreatedTime + temp_dispute.voteDeadlineInHours * 1 hours && now > temp_dispute.disputeCreatedTime + 24 hours);
        
        address buyer = temp_invoice.buyer;
        address seller = temp_invoice.seller;
        uint256 _invoiceAmount = temp_invoice.invoiceAmount;
        uint256 _mortgage = temp_invoice.mortgage;
        uint256 _reward = temp_dispute.reward;
        bool successfulTransfer = false;
        
        uint winningOption = 1;
        
        if(temp_dispute.toBuyer < temp_dispute.toSeller)
            winningOption = 2;
            
        else if(temp_dispute.toSeller < temp_dispute.toBurn)
            winningOption = 3; 
            
        if(winningOption == 1){
            temp_dispute.winner=temp_dispute.toBuyerVoters[0];
            
            _transfer(this, buyer, _invoiceAmount + _mortgage - _reward);
            
            userScore[buyer].num_transaction++;
            userScore[seller].num_transaction++;
            userScore[seller].score -= (_invoiceAmount + _mortgage);
            userScore[buyer].score += _invoiceAmount;
            successfulTransfer=true;
        }
        else if(winningOption == 2){
            temp_dispute.winner=temp_dispute.toSellerVoters[0];
            
            _transfer(this, seller, _invoiceAmount + _mortgage - _reward);
            
            userScore[buyer].num_transaction++;
            userScore[seller].num_transaction++;
            userScore[buyer].score -= _invoiceAmount;
            userScore[seller].score += _invoiceAmount + _mortgage;
            successfulTransfer=true;
        }
        else if(winningOption == 3){
            
            temp_dispute.winner=temp_dispute.toBurnVoters[0];
            
            require(balanceOf[this] >= _invoiceAmount + _mortgage - _reward);   // Check if the sender has enough
            balanceOf[this] -= (_invoiceAmount + _mortgage - _reward);            // Subtract from the sender
            totalSupply -= (_invoiceAmount + _mortgage - _reward);                      // Updates totalSupply

            userScore[buyer].num_transaction++;
            userScore[seller].num_transaction++;
            successfulTransfer=true;
        }
        else if(temp_dispute.toSeller==temp_dispute.toBuyer && temp_dispute.toSeller==temp_dispute.toBurn){
            temp_invoice.invoiceCreatedTime=now;
        }
        
        if(successfulTransfer){
            temp_invoice.completed=true;
            temp_dispute.completed=true;
        }
        
        return true;
    }
    
    function initiateDispute (
            string _invoiceID, 
            uint256 _reward, 
            uint _voteDeadlineInHours
        ) public returns (bool success){
            
        require(dispute[_invoiceID].created==false);
        require((invoice[_invoiceID].buyer==msg.sender || invoice[_invoiceID].seller==msg.sender) && _voteDeadlineInHours>=24 && ( 0 < _reward && _reward <= invoice[_invoiceID].invoiceAmount));
        
        Dispute storage temp_dispute=dispute[_invoiceID];
        
        temp_dispute.created=true;
        temp_dispute.reward=_reward;
        temp_dispute.voteDeadlineInHours = _voteDeadlineInHours;
        temp_dispute.disputeInitiatedBy=msg.sender;
        temp_dispute.disputeCreatedTime = now;

        return true;
    }
    
    function voteWithStake (
            string _invoiceID, 
            fundGoesTo _fundgoesTo
        ) public suchDisputeExists(_invoiceID) returns (bool success){
            
        Dispute storage temp_dispute=dispute[_invoiceID];
        
        require(now <= temp_dispute.disputeCreatedTime + temp_dispute.voteDeadlineInHours * 1 hours && temp_dispute.completed==false && balanceOf[msg.sender]>=temp_dispute.reward && userScore[msg.sender].score>=temp_dispute.reward && temp_dispute.stakeSince[msg.sender]==0);
        
        _transfer(msg.sender, this, temp_dispute.reward);
        
        temp_dispute.stakeSince[msg.sender]=now;
        temp_dispute.votedFor[msg.sender]=uint(_fundgoesTo);

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
    
    function withdrawStake (
            string _invoiceID 
        ) public suchDisputeExists(_invoiceID) returns (bool success){
            
        Dispute storage temp_dispute=dispute[_invoiceID];
        
        require(now > temp_dispute.stakeSince[msg.sender] + 24 hours);
        
        if(temp_dispute.completed && temp_dispute.winner==msg.sender)
           _transfer(msg.sender, this, 2 * temp_dispute.reward);
           
        else {
            if(!temp_dispute.completed){
                
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
    
    
}