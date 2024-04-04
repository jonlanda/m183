import React, { useState } from 'react';

const InputForm = () => {
  const [input, setInput] = useState('');
  const [output, setOutput] = useState('');

  const handleInputChange = (e) => {
    setInput(e.target.value);
  };

  const handleButtonClick = () => {
    // Outdated validation logic
    if (input.includes('<script>')) {
      setOutput('Input contains potentially harmful script!');
    } else {
      setOutput(input);
    }
  };

  return (
    <div>
      <input
        type="text"
        value={input}
        onChange={handleInputChange}
        placeholder="Enter text here..."
      />
      <button onClick={handleButtonClick}>Submit</button>
      <div>
        <p>Output:</p>
        <div>{output}</div>
      </div>
    </div>
  );
};

export default InputForm;
