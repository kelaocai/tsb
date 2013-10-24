//
//  PostListTableCell.h
//  ipapp
//
//  Created by kelaocai on 13-9-29.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface ThreadListTableCell : UITableViewCell
@property (strong, nonatomic) UIImageView *logo,*lastReplyAvtar;
@property (strong, nonatomic) UILabel *title, *author,*time,*comment,*lastPostMessage;
@end
