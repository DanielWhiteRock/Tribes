import React from 'react';
import ReactDOM from 'react-dom';

import GroupMemberFilterableList from '../../component/filter/GroupMemberFilterableList';

const groupMemberFilterableListElement = document.querySelector('#group-members-filterable-list');

if (groupMemberFilterableListElement) {
  const themeColor = groupMemberFilterableListElement.getAttribute('data-themecolor') || false,
        groupID = Number.parseInt(groupMemberFilterableListElement.getAttribute('data-groupid'), 10) || false,
        searchTerm = groupMemberFilterableListElement.getAttribute('data-searchterm') || false,
        gridType = groupMemberFilterableListElement.getAttribute('data-grid-type') || false;

  ReactDOM.render(
    <GroupMemberFilterableList  itemsPerPage={12}
                                themeColor={themeColor}
                                groupID={groupID}
                                searchTerm={searchTerm}
                                gridType={gridType}
    />,
    groupMemberFilterableListElement
  );
}