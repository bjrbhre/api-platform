import React from 'react';
import Admin from './Admin';

export default () => {
  return <Admin entrypoint={process.env.REACT_APP_API_ENTRYPOINT} />;
};
